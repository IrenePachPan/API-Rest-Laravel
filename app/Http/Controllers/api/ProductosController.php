<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Productos;
use Illuminate\Support\Facades\Validator; //para validar los datos de store

class ProductosController extends Controller{

    public function index(){
        $productos = Productos::all();
        if($productos->isEmpty()){ //mensaje de error, aunque se devuelve en formato json
            return response()->json(['message'=>'No hay productos en la tienda'], 200);
        }
        return response()->json(['productos' => $productos], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 
    //lo que he añadido de pretty es para que se muestre mejor en el index, el scape unicode es porque hay acentos y no se muestran bien y el slashes porque tampoco lo muestra bien
    }

    public function store(Request $request){
        // hay que validar primero
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255', //no hace falta poner unique porque es clave primary y de por si no deja que esté repetido
            'precio' => 'required|numeric|min:0|max:999.99', // Se ajusta al formato decimal (5,2) de las migraciones
            'descripcion' => 'required|string|max:200',
            'posología' => 'required|string|max:200', // Va con acento
            'efectos_secundarios' => 'required|string|max:200',
            'imagen' => 'required', // No pongo la validación de la url porque las imagenes son locales, no hay http ni .com
            'categoria' => 'required|string|max:255',
            'tratamiento' => 'required|string|max:255'
        ]);
        //si hay un error en la validación mostrará este mensaje y las validaciones que no se han cumplido
        if($validator-> fails()) {
               $data = [
                'message' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        };
        //antes de crear el producto verificamos que existe (la clave primaria es el nombre)
        //este método sirve para evitar que colapse el programa cuando sale la excepción de
        //MySql de entrada duplicada
        if (Productos::where('nombre', $request->nombre)->exists()) {
            $data=[
                'message' => 'El producto ya existe en la base de datos',
                'status' => 400
            ];
            return response()->json($data,400);
        }
        //si todos los campos han sido validados correctamente crea el producto
        $productos = Productos::create([
            'nombre' => $request->nombre,
            'precio' => $request->precio, 
            'descripcion' => $request->descripcion,
            'posología' => $request->posología, // Va con acento
            'efectos_secundarios' => $request->efectos_secundarios,
            'imagen' => $request->imagen, 
            'categoria' => $request->categoria,
            'tratamiento' => $request->tratamiento
        ]);

        if(!$productos){
            $data = [
                'message' => 'Error al crear el producto',
                'status' => 500
            ];
            return response()-> json($data, 500);
        }
        $data = [
            'message'=>'Producto creado exitosamente',
            'productos' => $productos,
            'status' => 201
        ];

        return response() ->json($data, 201);
    }

    public function storeAll(Request $request){
        // Validar que el request contenga un array de productos
        $validator = Validator::make($request->all(), [
            'productos' => 'required|array',  // Los datos deben ser un array
            'productos.*.nombre' => 'required|string|max:255|unique:productos', // Validación de nombre único para cada producto
            'productos.*.precio' => 'required|numeric|min:0|max:999.99',
            'productos.*.descripcion' => 'required|string|max:200',
            'productos.*.posología' => 'required|string|max:200',
            'productos.*.efectos_secundarios' => 'required|string|max:200',
            'productos.*.imagen' => 'required',
            'productos.*.categoria' => 'required|string|max:255',
            'productos.*.tratamiento' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }
    
        // Insertar todos los productos
        $productos = $request->productos;
    
        Productos::insert($productos);
        
        $data = [
            'message' => 'Productos creados exitosamente',
            'status' => 201
        ];
        return response()->json($data, 201);
    }
    



    public function show($nombre){
        $producto = Productos::find($nombre);
        if(!$producto){
            $data = [
                'message'=> 'Producto no encontrado',
                'status => 404'
            ];
            return response()->json($data, 404);
        }
        $data =[
            'producto'=>$producto,
            'status'=> 200
        ];
        return response()->json($data, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public function destroy($nombre){

        $producto = Productos::find($nombre);

        if(!$producto){
            $data = [
                'message'=> 'Producto no encontrado, no se puede eliminar',
                'status' => 404
            ];
            return response()->json($data, 404);
        }

        $producto->delete();

        $data =[
            'message'=> 'Producto eliminado',
            'status'=> 200
        ];
        return response()->json($data, 200);
    }

    public function update(Request $request, $nombre){

        $producto = Productos::find($nombre);

        if(!$producto){
            $data = [
                'message'=> 'Producto no encontrado, no se puede actualizar',
                'status' => 404
            ];
            return response()->json($data, 404);
        };

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255', 
            'precio' => 'required|numeric|min:0|max:999.99', 
            'descripcion' => 'required|string|max:200',
            'posología' => 'required|string|max:200', 
            'efectos_secundarios' => 'required|string|max:200',
            'imagen' => 'required', 
            'categoria' => 'required|string|max:255',
            'tratamiento' => 'required|string|max:255'
        ]);
        if($validator-> fails()) {
            $data = [
             'message' => 'Error en la validacion de los datos',
             'errors' => $validator->errors(),
             'status' => 400
            ];
            return response()->json($data, 400);
        };

        $producto->nombre = $request->nombre;
        $producto->precio = $request->precio;
        $producto->descripcion = $request->descripcion;
        $producto->posología = $request->posología; // Va con acento
        $producto->efectos_secundarios = $request->efectos_secundarios;
        $producto->imagen = $request->imagen; 
        $producto->categoria = $request->categoria;
        $producto->tratamiento = $request->tratamiento;

        $producto->save();

        $data=[
            'message' => 'Producto actualizado correctamente',
            'producto' => $producto,
            'status' => 200
        ];

        return response()->json($data, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }

    public function updatePartial(Request $request, $nombre){

        $producto = Productos::find($nombre);

        if(!$producto){
            $data = [
                'message'=> 'Producto no encontrado, no se ha podido actualizar parcialmente',
                'status' => 404
            ];

            return response()->json($data, 404);
        };

        //ahora los campos no son required, porque puede actualizar cuantos por petición
        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:255', 
            'precio' => 'numeric|min:0|max:999.99', 
            'descripcion' => 'string|max:200',
            'posología' => 'string|max:200', 
            'efectos_secundarios' => 'string|max:200',
            'imagen' => 'required', 
            'categoria' => 'string|max:255',
            'tratamiento' => 'string|max:255'
        ]);

        if($validator-> fails()) {
            $data = [
             'message' => 'Error en la validacion de los datos',
             'errors' => $validator->errors(),
             'status' => 400
            ];
            return response()->json($data, 400);
        };

        if($request->has('nombre')){
            $producto->nombre = $request->nombre;
        }
        if($request->has('precio')){
            $producto->precio = $request->precio;
        }
        if($request->has('descripcion')){
            $producto->descripcion = $request->descripcion;
        }
        if($request->has('posología')){
            $producto->posología = $request->posología;
        }
        if($request->has('efectos_secundarios')){
            $producto->efectos_secundarios = $request->efectos_secundarios;
        }
        if($request->has('imagen')){
            $producto->imagen = $request->imagen;
        }
        if($request->has('categoria')){
            $producto->categoria = $request->categoria;
        }
        if($request->has('tratamiento')){
            $producto->tratamiento = $request->tratamiento;
        }

        $producto->save();

        $data=[
            'message' => 'Producto actualizado correctamente',
            'producto' => $producto,
            'status' => 200
        ];

        return response()->json($data, 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
   public function index(Request $request)
{
    if ($request->ajax()) {
        // Si piden estadísticas del dashboard
        if ($request->has('stats')) {
            return response()->json([
                'total' => Product::count(),
                'stock' => Product::sum('cantidad'),
                'vencidos' => Product::whereDate('fecha_vencimiento', '<', now())->count()
            ]);
        }

        // Si es la lista normal de productos
        $query = Product::query();

        if ($request->has('sort') && $request->has('order')) {
            $query->orderBy($request->sort, $request->order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return response()->json($query->paginate(6));
    }

    return view('products.index');
}

    public function store(Request $request)
    {
        // Validaciones de respaldo en Backend
        $request->validate([
            'codigo_producto' => 'required|unique:products',
            'nombre_producto' => 'required',
            'cantidad' => 'required|numeric|min:0',
            'precio' => 'required|numeric|min:0',
            'fecha_ingreso' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_ingreso',
            'fotografia' => 'nullable|image|max:1536' // 1.5MB
        ]);

        $data = $request->all();

       // CAMBIO AQUÍ: Usamos el disco 'public' explícitamente
        if ($request->hasFile('fotografia')) {
            $path = $request->file('fotografia')->store('products', 'public'); 
            $data['fotografia'] = $path;
        }

        Product::create($data);

        return response()->json(['success' => 'Producto creado correctamente']);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'codigo_producto' => 'required|unique:products,codigo_producto,' . $product->id,
            'nombre_producto' => 'required',
            'cantidad' => 'required|numeric|min:0',
            'precio' => 'required|numeric|min:0',
            'fecha_ingreso' => 'required|date',
            'fecha_vencimiento' => 'required|date|after:fecha_ingreso',
            'fotografia' => 'nullable|image|max:1536'
        ]);

        $data = $request->all();

          if ($request->hasFile('fotografia')) {
            // Eliminar foto anterior
            if ($product->fotografia) {
                Storage::disk('public')->delete($product->fotografia); // Borrar del disco public
            }
            // Guardar nueva en disco public
            $path = $request->file('fotografia')->store('products', 'public');
            $data['fotografia'] = $path;
        }

        $product->update($data);

        return response()->json(['success' => 'Producto actualizado correctamente']);
    }

    // Método para obtener un solo producto (para editar en el modal)
    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        if ($product->fotografia) {
            Storage::delete($product->fotografia);
        }
        $product->delete();
        return response()->json(['success' => 'Producto eliminado']);
    }

     public function getNextCode()
    {
        // Buscamos el último producto creado para ver su código
        $lastProduct = Product::latest('id')->first();

        if (!$lastProduct) {
            // Si es el primero de la base de datos
            return response()->json(['code' => 'HM-00001']);
        }

        // Extraemos los numeros del código (ej: HM-00045 -> 45)
        // Asumimos que el formato siempre es HM-XXXXX
        $lastCode = $lastProduct->codigo_producto;
        
        // Quitamos "HM-" y convertimos a entero
        $number = (int) substr($lastCode, 3); 
        
        // Sumamos 1 y rellenamos con ceros a la izquierda
        $nextNumber = str_pad($number + 1, 5, '0', STR_PAD_LEFT);
        
        return response()->json(['code' => 'HM-' . $nextNumber]);
    }
}
 
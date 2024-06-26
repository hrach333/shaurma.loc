<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index (Request $request, $page = 1)
    {
        $title = 'Панель управление';
        $page = $request->route('page', 1);

        $products = Product::paginate(3);

        return \view('admin.admin',compact('title' , 'products' ));
    }
    //
    public function createProduct(Request $request)
    {
        // Создаем новую запись в базе данных
        $product = new Product();
        $product->product_name = 'Product Name'; // По умолчанию
        $product->price = '100'; // По умолчанию
        $product->product_img = 'default.png';
        $product->save();

        // Возвращаем успешный ответ
        return response()->json(['message' => 'Product created successfully'], 200);
    }
    public function saveProduct(Request $request)
    {
        $productId = $request->input('content_id');
        // Получаем объект контента по $productId
        $content = Product::findOrFail($productId);
        if ($request->input('product-name') !== null) {
           $content->product_name = $request->input('product-name');
        }

        if ($request->input('product-price') !== null) {
            $content->price = $request->input('product-price');
        }

        if ($request->input('description') !== null) {
            $content->description = $request->input('description');
        }
        //сохранение в БД
        $content->save();

        return response()->json(['success' => true]);
    }

    public function updateImage(Request $request) {
        $image = $request->imageName;
        $id = $request->productId;
        $content = Product::findOrFail($id);
        $content->product_img = $image;
        $content->save();
        return response()->json(['success' => true]);
    }
    // Метод для удаления продукта
    public function deleteProduct($id)
    {
        // Получаем идентификатор удаляемого продукта из запроса
        //$productId = $id;
        //dd($productId);
        // Находим продукт по его идентификатору
        $product = Product::find($id);

        // Проверяем, найден ли продукт
        if (!$product) {
            // Если продукт не найден, возвращаем сообщение об ошибке
            return response()->json(['error' => 'Продукт не найден ' . $id], 200);
        }

        // Удаляем продукт из базы данных
        $product->delete();

        // Возвращаем успешный ответ
        return response()->json(['message' => 'Продукт успешно удален'], 200);
    }
}

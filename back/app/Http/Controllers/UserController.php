<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone, // Añadido
            'address' => $request->address, // Añadido
            'birthdate' => $request->birthdate, // Añadido
            'role' => $request->role, // Añadido
        ]);
        

        // Generar token y guardarlo en la base de datos
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->auth_token = $token;
        $user->save();

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'auth_token' => $token,
        ], 201);
    }

    // Inicio de sesión
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Verificar si ya tiene un token activo
        if ($user->auth_token) {
            return response()->json([
                'message' => 'User already logged in',
                'auth_token' => $user->auth_token,
            ]);
        }

        // Generar nuevo token
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->auth_token = $token;
        $user->save();

        return response()->json([
            'message' => 'Login successful',
            'auth_token' => $token,
        ]);
    }

    // Cierre de sesión
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            // Eliminar el token almacenado en la base de datos
            $user->auth_token = null;
            $user->save();

            // Revocar todos los tokens de Sanctum del usuario
            $user->tokens()->delete();

            return response()->json(['message' => 'Logout successful']);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

       // Obtener todos los usuarios
       public function index()
       {
           // Verificar si el usuario autenticado es admin
           $user = Auth::user();
           if ($user->role !== 'admin') {
               return response()->json(['message' => 'Unauthorized'], 403); // Si no es admin, return 403
           }
   
           $users = User::all();
           return response()->json($users);
       }
   
       // Obtener un usuario por ID
       public function show($id)
       {
           // Verificar si el usuario autenticado es admin
           $user = Auth::user();
           if ($user->role !== 'admin') {
               return response()->json(['message' => 'Unauthorized'], 403); // Si no es admin, return 403
           }
   
           $user = User::find($id);
   
           if (!$user) {
               return response()->json(['message' => 'User not found'], 404);
           }
   
           return response()->json($user);
       }

       public function getCurrentUser(Request $request)
{
    return response()->json($request->user());
}

}

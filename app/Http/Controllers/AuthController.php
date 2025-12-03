<?php

namespace App\Http\Controllers;

use App\Services\AuthApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthApiService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Muestra el formulario de registro
     */
    public function mostrarRegistro()
    {
        return view('registro');
    }

    /**
     * Procesa el registro de un nuevo usuario
     */
    public function registro(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $resultado = $this->authService->registrar([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'password' => $request->password,
        ]);

        if (isset($resultado['error'])) {
            return back()->with('error', $resultado['error'])->withInput();
        }

        return redirect()->route('login')->with('success', 'Registro exitoso. Por favor inicia sesión.');
    }

    /**
     * Muestra el formulario de login
     */
    public function mostrarLogin()
    {
        return view('login');
    }

    /**
     * Procesa el inicio de sesión
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $resultado = $this->authService->login([
            'correo' => $request->correo,
            'password' => $request->password,
        ]);

        if (isset($resultado['error'])) {
            return back()->with('error', $resultado['error'])->withInput();
        }

        return redirect()->route('perfil')->with('success', 'Bienvenido!');
    }

    /**
     * Cierra la sesión
     */
    public function logout()
    {
        $this->authService->logout();
        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente');
    }
}



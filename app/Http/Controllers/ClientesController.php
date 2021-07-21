<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Clientes;
use App\Models\Localidades;
use App\Models\Zonas;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\Config;
use PhpParser\Node\Expr\Cast\Int_;
use Session;


class ClientesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = $request->all();
        $cliente = null;
        $cliente_sin_paginar = null;
        $num = null;
        $array_clientes = null;
        $total_clientes = Clientes::all();
        $modo_filtrado = false;
        $totalNumclientes = count($total_clientes);
        $zona = Zonas::all();
        $localidad = Localidades::all();
        $busqueda = $request->get('buscar');
        $zona_form = $request->get('zona');
        $localidad_form = $request->get('localidad');
        $forma = $request->get('forma');
        $tipo = $request->get('tipo');
        $dia = $request->get('dia');
        $frequencia = $request->get('frequencia');
        $ordenar = $request->get('order');
        $paginador = $request->get('paginador');
        $modo="";

        if ($zona_form || $forma || $tipo || $frequencia || $dia || $localidad_form) {
            $modo_filtrado = true;
            $cliente = Clientes::formapago($forma)->tipo($tipo)->dia($dia)->frequencia($frequencia)->zona($zona_form)->localidad($localidad_form)->paginate(10);
            $cliente_sin_paginar = Clientes::formapago($forma)->tipo($tipo)->dia($dia)->frequencia($frequencia)->zona($zona_form)->localidad($localidad_form);
        } else if ($busqueda) {
            $cliente = Clientes::buscar($busqueda)->paginate(10);
            $cliente_sin_paginar = Clientes::buscar($busqueda);
            $modo_filtrado = true;
        } else {
            $cliente = Clientes::Ordenarpor($ordenar)->paginate($paginador);
            $cliente_sin_paginar = Clientes::select('*')->get();
            $cliente_sin_paginar->toArray();
        }
        $num = Clientes::count();    //guarda el numero de clientes          
        return view('aplication.clientes', compact('cliente', 'zona', 'localidad', 'modo_filtrado', 'data', 'num', 'cliente_sin_paginar', 'ordenar','request'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $localidade = Localidades::all();
        $zona = Zonas::all();
        return view('aplication.addcliente', compact('localidade', 'zona'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'direccion' => 'required',
            'telefono' => 'nullable',
            'zona' => 'nullable',
            'localidad' => 'nullable',          //por defecto 'Arrecife'
            'forma_pago' => 'required',
            'tipo_cliente' => 'required',
            'dia_reparto' => 'nullable',        //por defecto 'sin dia'
            'frequencia_reparto' => 'nullable', //por defecto 'otros'
            'condiciones' => 'nullable',
            'observaciones' => 'nullable'
        ]);
        Clientes::create($request->all());
        $request->session()->flash('mensaje','nuevo');
        return redirect()->route('clientes.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Clientes $cliente)
    {
        return view('aplication.showcliente', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Clientes $cliente)
    {        
        $zona = Zonas::all();
        $localidade = Localidades::all();
        return view('aplication.editcliente', compact('cliente', 'zona', 'localidade'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Clientes $cliente)
    {
        $request->validate([
            'nombre' => 'required',
            'direccion' => 'required',
            'telefono' => 'nullable',
            'zona' => 'nullable',
            'localidad' => 'required',
            'forma_pago' => 'required',
            'tipo_cliente' => 'nullable',
            'dia_reparto' => 'nullable',
            'frequencia_reparto' => 'nullable',
            'condiciones' => 'nullable',
            'observaciones' => 'nullable'
        ]);
        $cliente->update($request->all());
        $request->session()->flash('mensaje','actualizar');
        return redirect()->route('clientes.index');
       
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Clientes $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('mensaje', 'eliminar');
    }


    /**
     * Crea un pdf con los datos seleccionados en pantalla
     * @param $clientes = listado de id de clientes de la vista principal
     * 
     */
    public function crearPDF($clientes)
    {
        //dd($clientes);
        //------convierte el array clientes en array de enteros-------
        $id_clientes = array_map('intval', json_decode($clientes, true));

        //----------selecciona los clientes a mostrar en el pdf por su id-------------     
        $clientePDF = Clientes::whereIn('id', $id_clientes)->get();


        //------------------muestra el pdf en otra vista---------------  
        view()->share('cliente',  $clientePDF);

        $pdf = PDF::loadView('pdf.pdf_view',  $clientePDF);

        return $pdf->stream('hoja', '.pdf');
    }
    
}

@extends('adminlte::page')

@section('title', '運転適性検査登録')

@section('content_header')
    <h1>運転適性検査登録</h1>
@stop
@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <x-forms.aptitude-driving.form :isCreate="true" :testtype="$test_type" :model="$data" method="POST" :seq="$seq"
                    route="{{ route('aptitude-driving.store', ['ledger_id' => $data->ledger_id]) }}" />
            </div>
        </div>
    </div>
@stop

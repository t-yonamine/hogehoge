@extends('adminlte::page')

@section('title', 'スタッフ登録')

@section('content_header')
    <h1>スタッフ登録</h1>
@stop
@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <x-forms.school-staff.form :data="$data" :user="$user" :isCreate="true"
                    route="{{ route('school-staff.store') }}" method='POST' />
            </div>
        </div>
    </div>
@stop

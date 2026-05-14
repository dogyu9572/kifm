@extends('layouts.frontend')
@section('title', $gName . ' | ' . $sName)
@section('gName', $gName)
@section('sName', $sName)
@section('content')
<main class="sub_area">

<section class="scon core_values_wrap" aria-labelledby="core-values-heading">
	<div class="inner">
		<h1 class="sub_title" id="core-values-heading">{{ $sName }}</h1>
		
	</div>
</section>
	
</main>

@endsection
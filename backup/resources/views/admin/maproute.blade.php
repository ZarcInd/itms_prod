@extends('admin/layouts/master')
@section('container')
<main class="main-content" >
    <div class="container-fluid">
        <h2 class="mb-4">Route Replay</h2>

        <!-- IFRAME WRAPPER WITH NO SCROLL & 100% HEIGHT -->
        <div style="width: 100%; height: 90vh;">
            <iframe 
                src="https://mtc.primeedge.co.in/latest_data_view/route_replay.html" 
                style="width: 100%; height: 100%; border: none;">
            </iframe>
        </div>
    </div>
</main>


@endsection
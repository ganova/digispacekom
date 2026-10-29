@if ($errors->any())
  <div class="errs" role="alert">
    <b>Mohon lengkapi dulu:</b>
    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
  </div>
@endif

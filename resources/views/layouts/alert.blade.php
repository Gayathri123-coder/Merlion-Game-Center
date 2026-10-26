@if (Session::has('success'))
<div class="alert alert-success">	<button type="button" class="close" data-dismiss="alert">x</button>
    <strong><center>{{session('success')}}</center></strong>
</div>
@endif
@if (Session::has('info'))
<div class="alert alert-info">	<button type="button" class="close" data-dismiss="alert">x</button>
    <strong><center>{{session('info')}}</center></strong>
</div>
@endif
@if (Session::has('warning'))
<div class="alert alert-warning">	<button type="button" class="close" data-dismiss="alert">x</button>
    <strong><center>{{session('warning')}}</center></strong>
</div>
@endif
@if (Session::has('danger'))
<div class="alert alert-danger">	<button type="button" class="close" data-dismiss="alert">x</button>
    <strong><center>{{session('danger')}}</center></strong>
</div>
@endif


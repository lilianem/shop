<div class="row">
  <div class="col-sm-12">
    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
      <button class="navbar-toggler navbar-toggler-right hidden-lg-up" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
     
      <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
      <a class="navbar-brand" href="{{ URL::to('./') }}">Home</a>
      </li>
       @auth
                <li class="nav-item">
                <a id="logout" class="nav-link" href="{{ route('logout') }}">@lang('Déconnexion')</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hide">
                {{ csrf_field() }}
                </form>
                </li>
       @endauth
       </ul>
      
      
    </nav>
  </div>
</div>

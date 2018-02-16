# absolvent/bundle-api

## JWT Authentication Guard

### Enable in project

1. Add `Absolvent\api\Providers\AuthServiceProvider` to `config/app.php` providers.
2. Create `config/jwt.php` similar to `config/jwt.php` in this bundle
3. Change `defaults.guard` to `jwt` in `config/auth.php`
4. Add 

```
'jwt' => [
    'driver' => 'jwt',
    'provider' => 'users',
],
```

to `guards` in `config/auth.php`

4. Add `JWT_SECRET` variable to `.env` (eq. `JWT_SECRET=SvfJknJLYWwvadkCLVE7HIzn2JpWDkXv`)

NOTE: `JWT_SECRET` should be te same as in `microservice-users`

### Usage

Generally all Laravel authorization and authentication functionality
should work out of the box when `JwtAuthenticationGuard` is configured properly.

#### Get jwt token / user information

```
use Illuminate\Support\Facades\Auth;

$user = Auth::user(); // returns JwtUser or null
$user = Auth::authenticate() // returns JwtUser or throws Exception
$jwtToken = Auth::getName(); // returns jwt token
$userSub = Auth::id(); // returns user email (jwt token `sub` claim)
```

User information are taken from jwt token so there are very basic.
To get extended user information you have to issue call to `microservice-users`.

#### Permission based endpoint authentication

Add `permission` or `can` middleware to endpoint controller

```
class EndpointController extends \Absolvent\api\Http\Controller
{
    public function __construct()
    {
        $this->middleware('permission:TALENTDAYS_ADMIN|TALENTDAYS_AREA');
    }
    
    // ...
}

```

In above example only user with `TALENTDAYS_ADMIN` or `TALENTDAYS_AREA` can access the endpoint

## Allow sending PATH requests with multipart-form content type

Add `Absolvent\api\Http\Middleware\PreparePatchMultiPartForm` to `Absolvent\api\Http\Kernel::$middleware`

Make sure that `PreparePatchMultiPartForm` is after `ValidatePostSize`

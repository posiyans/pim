<?php

namespace App\Http\Controllers\Ppsd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FileMyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $data= ["args"=> [],
//            "data"=> "",
//            "files"=> [
//                "file"=> "data:image/png;base6        //4,iVBORw0KGgoAAAANSUhEUgAAAScAAADoCAYAAACtkyKUAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAC4jAAAuIwF4pT92AAAAB3RJTUUH4wERDCQH71dXpwAAIABJREFUeNrsvXeYXdV99/vZ5Zx9epteJM2oNyQhgSQkkChC9GaKE2PHHeK4vC6JSd5737xxbpzYzk3eOI5j4zi2CS6YatOLEEg09d5GZXqfM6f3Xdb9Y8gFTBMwDCOxPs+jR8+jM5pzzm+v9d2/39q/ogghkEgkksmGKk0gkUikOEkkEokUJ4lEIsVJIpFIpDhJJBIpThKJRPIBoUsTSCQTT6YX8dIJCAMr16JIi7weReY5SSQTy94tiDuet4lN0cioECzC5y+C+iYpUjKsk0g+IHZsQty5BQp+jawfKoOQUOD5krSNFCeJ5AOibSfinh1gBqEShd4dOXIJ8DfB7BnSPn+IPHOSSCaIe3dAvgFUDd…kLiIfx8bQ/3kKMyVgGbkp4nNW+G+RgkmIdM6cZkqmmDkeBZXwkxVCYQSKJfj08Pl+B7Uf/VBbgW4kxAch023wlN1EkxCKiexAL7YCnNJM9aAggzsOf3Jf3+6F/Xsr1D/NAITHnBNQDgJdz8IT7VIMAmpnMQCWedGq1qCOvohlCkoDcBACrVo1TlBE4e3OlFvd0O8DKweSI9DpxeevBM2yT4m8RnTlFLSC1eZfWHUj16FasPAVK6jNPB74KYshKbguAOyBiTnYNScP+piDkNuCWy7Fe6T38oJCSdRKH/Zi/rwLfAmFF6LBr6P35ObBAegNEiZIZkFaxm0bYEnfIQqoFx6T0g4iYJ6fB9qoBMcM1DhhpIq0GIwFYRcJcQMcK+EJ9bDBqmWhISTKKZ/mER1nwTDAu7FkM6CEYJFlbCxErZIKAkJJyGEuHCylUAIIeEkhBASTkIICSchhJBwEkJIOAkhhISTEEKc5X8AC3tsQNvSu5kAAAAASUVORK5CYII=",
//                ],
//            'form'=>[]
//    ];

        return $this->response('ok');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $md5 = $id;
            $path = '../storage/app/file/ppsd/pr/' . substr($md5, 0, 2) . '/' . $md5;
            if (file_exists($path)) {
                return response()->download($path);
            }
            return $this->response(['error' => $path], 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

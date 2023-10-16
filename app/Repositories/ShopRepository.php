<?php

namespace App\Repositories;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Support\Facades\File;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class ShopRepository.
 */
class ShopRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function all()
    {
        return Shop::all();
    }

    public function find($id)
    {
        //   $shop = Shop::findOrFail($id);
        $shop = Shop::with('user', 'countries', 'states', 'cities')->where('id', $id)->first();
        return $shop;
    }

    public function create($request)
    {
        $coverImageName = [];
        $shopkeeperImageName = [];
        $coverPhotoArr = $request->cover_photo_name;
        // print_r($coverPhotoArr);exit();
        foreach ( $coverPhotoArr as $coverPhoto) {
            /* $imageName = uniqid() . $coverPhoto->getClientOriginalName();
            $coverPhoto->move(public_path('/images/shop/coverPhoto'), $imageName); */
            array_push($coverImageName, $coverPhoto);
        }
        $nrcPhotoArr = $request->nrc_photo_name;
        foreach ($nrcPhotoArr as $shopkeeperNrc) {
            /* $imageName = uniqid() . $shopkeeperNrc->getClientOriginalName();
            $shopkeeperNrc->move(public_path('/images/shop/shopkeeperNrc'), $imageName); */
            array_push($shopkeeperImageName, $shopkeeperNrc);
        }
        // $logo = $request->file('logo');

        // $logoFileName = uniqid().$logo->getClientOriginalName();
        // $logo->move(public_path('/images/shop/logo'),$logoFileName);

        /* $orgFileName = $logo->getClientOriginalName();
        $subFileName = str_replace(" ", "", $orgFileName);
        $logoFileName = uniqid() . $subFileName;
        $logo->move(public_path('/images/shop/logo'), $logoFileName); */

        $agent_code = $request->agent_code;
        $user = User::where('agent_code', $agent_code)->first();
        if ($user) {
            return Shop::create([
                'name' => $request->name,
                'logo' =>  $request->logo_image_name,
                'cover_photo' => json_encode($coverImageName),
                'user_id' => $request->user_id,
                'shopkeeper_nrc' => json_encode($shopkeeperImageName),
                'nrc' => $request->nrc,
                'agent_user_id' => $request->agent_user_id,
                'lat' => $request->lat,
                'long' => $request->long,
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'address' => $request->address,
                'agent_code' => $request->agent_code
            ]);
        }
    }

    public function update($request, $id)
    {
        $shop = Shop::where('id', $id)->first();
        $keeperNrc = $request->file("shopkeeper_nrc");
        $typeLength = strlen($request->type);
        $logo = $request->file('logo');
        $delLocal =  json_decode($shop->cover_photo);
        $delLocalKeeper = json_decode($shop->shopkeeper_nrc);
        $coverImageName = [];
        $shopkeeperArray = [];
        $new_replace_delLocalKeeper = [];
        if ($keeperNrc) {
            $imageCounter = count($keeperNrc);
            // 'one photo change'
            if ($imageCounter == 1) {
                [$type, $subType] = str_split($request->type, 1); // [1,2]
                if ($subType == '0') {
                    $imageName = uniqid() . $keeperNrc[0]->getClientOriginalName();;
                    $keeperNrc[0]->move(public_path('/images/shop/shopkeeperNrc'), $imageName);
                    $replacements = array(0 => $imageName);
                    $new_replace_delLocalKeeper = array_replace($delLocalKeeper,  $replacements);
                    unlink(public_path("images/shop/shopkeeperNrc/" . $delLocalKeeper[$subType]));
                }
                if ($subType == '1') {
                    $imageName = uniqid() . $keeperNrc[0]->getClientOriginalName();;
                    $keeperNrc[0]->move(public_path('/images/shop/shopkeeperNrc'), $imageName);
                    $replacements = array(1 => $imageName);
                    $new_replace_delLocalKeeper = array_replace($delLocalKeeper,  $replacements);
                    unlink(public_path("images/shop/shopkeeperNrc/" . $delLocalKeeper[$subType]));
                }
            }
            // all Shop keeper nrc delete and update
            if ($imageCounter == 2) {
                // [$type , $oneType ,$twoType] = str_split($request->type,1); //122 [1,2,2]
                foreach ($keeperNrc as $keeper) {
                    $imageName =  uniqid() . $keeper->getClientOriginalName();
                    $keeper->move(public_path('/images/shop/shopkeeperNrc'), $imageName);
                    array_push($new_replace_delLocalKeeper, $imageName);
                }
                foreach ($delLocalKeeper as $localImage) {
                    unlink(public_path("images/shop/shopkeeperNrc/" . $localImage));
                }
            }
        }
        switch ($typeLength) {
            case $typeLength == '2': //One Photo change
                [$type, $subType] = str_split($request->type, 1); // [1,2]
                $new_replace_delLocal = $this->uploadImage($type, $subType, $request->file('cover_photo'), $delLocal);
                break;
            case $typeLength == '3': //Two Photo change
                [$type, $oneType, $twoType] = str_split($request->type, 1); //122 [1,2,2]
                $new_replace_delLocal = $this->twoUploadImage($type, $oneType, $twoType, $request->file('cover_photo'), $delLocal);
                break;
            case $typeLength == '4': //Three Photo change
                [$type, $oneType, $twoType, $threeType] = str_split($request->type, 1); //12 [1,2]
                $new_replace_delLocal = $this->threeUploadImage($type, $oneType, $twoType, $threeType, $request->file('cover_photo'), $delLocal);
                // $new_replace_delLocalKeeper = $this->threeuploadKeeperImage($type,$oneType,$twoType,$request->file('shopkeeper_nrc'),$delLocalKeeper);
                break;
            case $typeLength == '5': //Four Photo change
                [$type, $oneType, $twoType, $threeType, $fourType] = str_split($request->type, 1); //12 [1,2]
                $new_replace_delLocal = $this->fourUploadImage($type, $oneType, $twoType, $threeType, $fourType, $request->file('cover_photo'), $delLocal);
                break;
            default:
                break;
        }

        // logo image update
        if ($request->file('logo')) {
            unlink(public_path("images/shop/logo/" . $shop->logo));

            // $logoFileName = uniqid().$logo->getClientOriginalName();
            // $logo->move(public_path('/images/shop/logo'),$logoFileName);

            $orgFileName = $logo->getClientOriginalName();
            $subFileName = str_replace(" ", "", $orgFileName);
            $logoFileName = uniqid() . $subFileName;
            $logo->move(public_path('/images/shop/logo'), $logoFileName);
        } else {
            $logoFileName = $shop->logo;
        }
        //cover photo image update  Type == 1 all upload Cover Photo
        if ($request->type == '1' || $request->type == '2') {
            if ($cv =  $request->file('cover_photo')) {
                foreach ($cv as $newCover) {
                    $imageName = uniqid() . $newCover->getClientOriginalName();
                    $newCover->move(public_path('/images/shop/coverPhoto'), $imageName);
                    array_push($coverImageName, $imageName);
                    $new_replace_delLocal = $coverImageName;
                }
                foreach ($delLocal as $delCoverLocal) {
                    unlink(public_path("images/shop/coverPhoto/" . $delCoverLocal));
                }
            }
            if ($shopkeeper =  $request->file('shopkeeper_nrc')) {
                foreach ($shopkeeper as $keeper) {
                    $imageName = uniqid() . $keeper->getClientOriginalName();
                    $keeper->move(public_path('/images/shop/shopkeeperNrc'), $imageName);
                    array_push($shopkeeperArray, $imageName);
                    $new_replace_delLocalKeeper = $shopkeeperArray;
                }
                foreach ($delLocalKeeper as $delKeeper) {
                    unlink(public_path("images/shop/shopkeeperNrc/" . $delKeeper));
                }
            }
        }
        // if($request->type == '2' || $request->type == '1'){
        //     if($shopkeeper =  $request->file('shopkeeper_nrc')){
        //         foreach($shopkeeper as $keeper){
        //             $imageName = uniqid().$keeper->getClientOriginalName();
        //             $keeper->move(public_path('/images/shop/shopkeeperNrc'),$imageName);
        //             array_push( $shopkeeperArray,$imageName);
        //             $new_replace_delLocalKeeper=$shopkeeperArray;
        //         }
        //         foreach($delLocalKeeper as $delKeeper)
        //         {
        //             unlink(public_path("images/shop/shopkeeperNrc/".$delKeeper));
        //         }
        //     }
        // }
        //Shopkeeper For Fix Empty Array

        $updateData = [
            'name' => $request->name,
            'user_id' => $request->user_id,
            'nrc' => $request->nrc,
            'agent_user_id' => $request->agent_user_id,
            'lat' => $request->lat,
            'long' => $request->long,
            'country_id' => $request->country_id,
            'state_id' => $request->state_id,
            'city_id' => $request->city_id,
            'address' => $request->address,
            'logo' => $logoFileName,
            'cover_photo' => json_encode($new_replace_delLocal),
            'shopkeeper_nrc' => json_encode($new_replace_delLocalKeeper),
            'agent_code' => $request->agent_code
        ];
        return Shop::find($id)->update($updateData);
    }

    public function delete($id)
    {
        $data = Shop::where('id', $id)->first();
        $logofileName = $data->logo;
        $cvfileName = json_decode($data->cover_photo);
        $nrcfileName = json_decode($data->shopkeeper_nrc);

        if (File::exists(public_path() . '/images/shop/logo/' . $logofileName)) {
            File::delete(public_path() . '/images/shop/logo/' . $logofileName);
        }
        foreach ($cvfileName as $cv) {
            if (File::exists(public_path() . '/images/shop/coverPhoto/' . $cv)) {
                File::delete(public_path() . '/images/shop/coverPhoto/' . $cv);
            }
        }
        foreach ($nrcfileName as $nrc) {
            if (File::exists(public_path() . '/images/shop/shopkeeperNrc/' . $nrc)) {
                File::delete(public_path() . '/images/shop/shopkeeperNrc/' . $nrc);
            }
        }
        return $data->delete();
    }
    public function model()
    {
        //return YourModel::class;
    }
    public function uploadImage($type, $subType, $cv, $delLocal)
    {
        switch ($type == '1') {
            case $subType == '0':
                // local photo del
                unlink(public_path("images/shop/coverPhoto/" . $delLocal[$subType]));
                //Update upload sutyp 1
                $imageName = uniqid() . $cv->getClientOriginalName();
                $cv->move(public_path('/images/shop/coverPhoto'), $imageName);
                $replacements = array(0 => $imageName);
                $new_replace_delLocal = array_replace($delLocal,  $replacements);
                return $new_replace_delLocal;
                break;
            case $subType == '1':

                // local photo del
                unlink(public_path("images/shop/coverPhoto/" . $delLocal[$subType]));
                //Update upload sutyp 1
                $imageName = uniqid() . $cv->getClientOriginalName();
                $cv->move(public_path('images/shop/coverPhoto'), $imageName);
                $replacements = array(1 => $imageName);

                $new_replace_delLocal = array_replace($delLocal,  $replacements);
                return $new_replace_delLocal;
                break;
            case $subType == '2':
                // local photo del
                unlink(public_path("images/shop/coverPhoto/" . $delLocal[$subType]));
                //Update upload sutyp 1
                $imageName = uniqid() . $cv->getClientOriginalName();
                $cv->move(public_path('/images/shop/coverPhoto'), $imageName);
                $replacements = array(2 => $imageName);
                $new_replace_delLocal = array_replace($delLocal,  $replacements);
                return $new_replace_delLocal;
                break;
            case $subType == '3':
                // local photo del
                unlink(public_path("images/shop/coverPhoto/" . $delLocal[$subType]));
                //Update upload sutyp 1
                $imageName = uniqid() . $cv->getClientOriginalName();
                $cv->move(public_path('/images/shop/coverPhoto'), $imageName);
                $replacements = array(3 => $imageName);
                $new_replace_delLocal = array_replace($delLocal,  $replacements);
                return $new_replace_delLocal;
                break;
            case $subType == '4':
                // local photo del
                unlink(public_path("images/shop/coverPhoto/" . $delLocal[$subType]));
                //Update upload sutyp 1
                $imageName = uniqid() . $cv->getClientOriginalName();
                $cv->move(public_path('/images/shop/coverPhoto'), $imageName);
                $replacements = array(4 => $imageName);
                $new_replace_delLocal = array_replace($delLocal,  $replacements);
                return $new_replace_delLocal;
                break;
            default:

                break;
        }
    }

    public function twouploadImage($type, $oneType, $twoType, $cv, $delLocal)
    {
        if ($type == '1') {
            //Update upload one = 0 and two = 1
            $k = 0;
            $new_replace_delLocal = $delLocal;
            foreach ($cv as $c) {
                $imageName = uniqid() . $c->getClientOriginalName();
                $c->move(public_path('/images/shop/coverPhoto'), $imageName);
                $new_replace_delLocal[$k] = $imageName;
                $k++;
                // $replacements = array($k => $imageName);
                // $k = $k + 1;
                // array_push($new_replace_delLocal,array_replace($delLocal,  $replacements ));
            }
            unlink(public_path("images/shop/coverPhoto/" . $delLocal[$oneType]));
            unlink(public_path("images/shop/coverPhoto/" . $delLocal[$twoType]));


            return $new_replace_delLocal;
        }
    }
    public function threeuploadImage($type, $oneType, $twoType, $threeType, $cv, $delLocal)
    {
        if ($type == '1') {

            $k = 0;
            $new_replace_delLocal = $delLocal;
            foreach ($cv as $c) {
                $imageName = uniqid() . $c->getClientOriginalName();
                $c->move(public_path('/images/shop/coverPhoto'), $imageName);
                $new_replace_delLocal[$k] = $imageName;
                $k++;
                // $replacements = array($k => $imageName);
                // $k = $k + 1;
                // array_push($new_replace_delLocal,array_replace($delLocal,  $replacements ));
            }
            unlink(public_path("images/shop/coverPhoto/" . $delLocal[$oneType]));
            unlink(public_path("images/shop/coverPhoto/" . $delLocal[$twoType]));
            unlink(public_path("images/shop/coverPhoto/" . $delLocal[$threeType]));

            return $new_replace_delLocal;
        }
    }
    public function fouruploadImage($type, $oneType, $twoType, $threeType, $fourType, $cv, $delLocal)
    {
        if ($type == '1') {

            $k = 0;
            $new_replace_delLocal = $delLocal;
            foreach ($cv as $c) {
                $imageName = uniqid() . $c->getClientOriginalName();
                $c->move(public_path('/images/shop/coverPhoto'), $imageName);
                $new_replace_delLocal[$k] = $imageName;
                $k++;
                // $replacements = array($k => $imageName);
                // $k = $k + 1;
                // array_push($new_replace_delLocal,array_replace($delLocal,  $replacements ));
            }
            unlink(public_path("images/shop/coverPhoto/" . $delLocal[$oneType]));
            unlink(public_path("images/shop/coverPhoto/" . $delLocal[$twoType]));
            unlink(public_path("images/shop/coverPhoto/" . $delLocal[$threeType]));
            unlink(public_path("images/shop/coverPhoto/" . $delLocal[$fourType]));

            return $new_replace_delLocal;
        }
    }
    public function uploadShopkeeperImage($type, $subType, $shopkeeperNrc, $delLocalKeeper)
    {
        if ($subType == '0') {
            $imageName = uniqid() . $shopkeeperNrc->getClientOriginalName();
            $shopkeeperNrc->move(public_path('/images/shop/shopkeeperNrc'), $imageName);
            $replacements = array(0 => $imageName);
            $new_replace_delLocalKeeper = array_replace($delLocalKeeper,  $replacements);
            unlink(public_path("images/shop/shopkeeperNrc/" . $delLocalKeeper[$subType]));
            return $new_replace_delLocalKeeper;
        }
        if ($subType == '1') {
            unlink(public_path("images/shop/shopkeeperNrc/" . $delLocalKeeper[$subType]));
            $imageName = uniqid() . $shopkeeperNrc->getClientOriginalName();
            $shopkeeperNrc->move(public_path('/images/shop/shopkeeperNrc'), $imageName);
            $replacements = array(1 => $imageName);

            $new_replace_delLocalKeeper = array_replace($delLocalKeeper,  $replacements);
            return $new_replace_delLocalKeeper;
        }
    }

    public function twouploadKeeperImage($type, $oneType, $twoType, $shopNrc, $delLocalKeeper)
    {
        if ($type == '2') {
            print_r($delLocalKeeper);

            // echo $twoType;exit();
            //Update upload one = 0 and two = 1
            $k = 0;
            $new_replace_delLocalKeeper = $delLocalKeeper;
            foreach ($shopNrc as $c) {
                $imageName = uniqid() . $c->getClientOriginalName();
                $c->move(public_path('/images/shop/shopkeeperNrc'), $imageName);
                $new_replace_delLocalKeeper[$k] = $imageName;
                $k++;
                // $replacements = array($k => $imageName);
                // $k = $k + 1;
                // array_push($new_replace_delLocal,array_replace($delLocal,  $replacements ));
            }
            unlink(public_path("images/shop/shopkeeperNrc/" . $delLocalKeeper[0]));
            unlink(public_path("images/shop/shopkeeperNrc/" . $delLocalKeeper[1]));


            return $new_replace_delLocalKeeper;
        }
    }

    // public function twouploadKeeperImage($type,$oneType,$twoType,$shopNrc,$delLocalKeeper){
    //     if($type == '2')
    //     {
    //             //Update upload one = 0 and two = 1
    //             $k = 0;
    //             $new_replace_delLocalKeeper =[];
    //             foreach($shopNrc as $c){
    //                 $imageName = uniqid().$c->getClientOriginalName();
    //                 $c->move(public_path('/images/shop/shopkeeperNrc'),$imageName);
    //                 $replacements = array($k => $imageName);
    //                 $k = $k + 1;
    //                 array_push($new_replace_delLocalKeeper,array_replace($delLocalKeeper,  $replacements ));
    //             }
    //             unlink(public_path("images/shop/shopkeeperNrc/".$delLocalKeeper[$oneType]));
    //             unlink(public_path("images/shop/shopkeeperNrc/".$delLocalKeeper[$twoType]));
    //             return $new_replace_delLocalKeeper[0];
    //     }
    //     if($type == '2')
    //     {
    //             //Update upload one = 0 and two = 1
    //             $k = 0;
    //             $new_replace_delLocalKeeper =[];
    //             foreach($shopNrc as $c){
    //                 $imageName = uniqid().$c->getClientOriginalName();
    //                 $c->move(public_path('/images/shop/shopkeeperNrc'),$imageName);
    //                 $replacements = array($k => $imageName);
    //                 $k = $k + 1;
    //                 array_push($new_replace_delLocalKeeper,array_replace($delLocalKeeper,  $replacements ));
    //             }
    //             unlink(public_path("images/shop/shopkeeperNrc/".$delLocalKeeper[$oneType]));
    //             unlink(public_path("images/shop/shopkeeperNrc/".$delLocalKeeper[$twoType]));
    //             return $new_replace_delLocalKeeper;
    //     }
    // }

}

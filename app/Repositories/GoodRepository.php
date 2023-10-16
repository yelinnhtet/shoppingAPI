<?php

namespace App\Repositories;

use App\Models\Good;
use App\Models\Shop;
use App\Models\Good_para;
use App\Models\Good_spec;
use App\Models\GoodRating;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class GoodRepository.
 */
class GoodRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function all()
    {
        // 60*60*24 one Day Timer
        // return Cache::remember('goods', 60*60*24 ,function () {
                    $goods = Good::with('shop', 'category', 'good_specs', 'good_paras.currency')
                            ->select('goods.*', DB::raw('(SELECT MAX(rating) FROM good_ratings WHERE good_id = goods.id) as max_rating'))
                            ->orderBy('id', 'desc')
                            ->paginate(10);

    return $goods;

    }

    public function find($id)
    {
        return Good::with('shop', 'category','good_specs','good_paras.currency')
        ->findOrFail($id);
    }

    public function create($request)
    {
        $photo = [];
        /* foreach ($request->file('photo') as $coverPhoto) {
            $imageName = uniqid().$coverPhoto->getClientOriginalName();
            $coverPhoto->move(public_path('/images/good/goodPhoto'),$imageName);
            array_push( $photo,$imageName);
        } */
        $photoArr = $request->image_name;
        foreach ( $photoArr as $pic) {
            array_push($photo, $pic);
        }

        //adding to goods
        $newGood = Good::create([
            'name' => $request->name,
            'photo' => json_encode($photo),
            'shop_id' => $request->shop_id,
            'price' => $request->price,
            'discount' => $request->discount,
            'description' => $request->description,
            'category_id' => $request->category_id,
        ]);
        $newGoodId = $newGood->id;
        $qty=0;
        $para_names = $request->para_name;
        $para_values = $request->para_value;
        if (count($para_names) === count($para_values)) {
            $currencyId = $request->currency_id;

            foreach ($para_values as $para_value) {
                if (is_numeric($para_value)) {
                    $qty += (float) $para_value;
                }

                Good_para::create([
                    'name' => array_shift($para_names),
                    'value' => $para_value,
                    'currency_id' => $currencyId,
                    'good_id' => $newGoodId,
                ]);
            }
            $newGood->qty = $qty;
            $newGood->save();
        }

        //adding to good_specs
        $spec_names = $request->spec_name;
        $spec_values = $request->spec_value;
        if (count($spec_names) === count($spec_values)) {
            foreach ($spec_values as $spec_value) {
                Good_spec::create([
                    'name' => array_shift($spec_names),
                    'value' => $spec_value,
                    'good_id' => $newGoodId,
                ]);
            }
        }
        $userId=Shop::where('id',$request->shop_id)->select('user_id')->first();
        $goodRating = GoodRating::create([
            'user_id' => $userId['user_id'],
            'good_id' => $newGoodId,
            'rating' => 0,

        ]);
        return $newGood;

    }

    public function delete($id)
    {
        $data = Good::where('id',$id)->first();
        $photo=$data->photo;
        $photoArray = json_decode($photo);
        foreach ($photoArray as $coverPhoto) {
            if(File::exists(public_path().'/images/good/goodPhoto/'.$coverPhoto)){
                File::delete(public_path().'/images/good/goodPhoto/'.$coverPhoto);
            }
        }
        $good=$data->delete();
        return $good;
    }

    public function model()
    {
        //return YourModel::class;
    }

    public function update($request,$id)
    {
        $typeLength = strlen($request->type);

        $good = Good::where('id',$id)->first();

        $coverImageName = [];
        $delLocal =  json_decode($good->photo);
        // return $delLocal;
        switch ($typeLength) {
            case $typeLength == '2'://One Photo change
                [$type,$subType] = str_split($request->type,1); //12 [1,2]
                $new_replace_delLocal = $this->uploadImage($type,$subType,$request->file('photo'),$delLocal);
                break;
            case $typeLength == '3'://Two Photo change
                // return $request;
                [$type , $oneType ,$twoType] = str_split($request->type,1); //122 [1,2,2]
                // return $request->file('photo');
                $new_replace_delLocal = $this->twoUploadImage($type,$oneType,$twoType,$request->file('photo'),$delLocal);
                break;
            case $typeLength == '4'://Three Photo change
                [$type , $oneType ,$twoType,$threeType] = str_split($request->type,1); //12 [1,2]
                $new_replace_delLocal = $this->threeUploadImage($type,$oneType,$twoType,$threeType,$request->file('photo'),$delLocal);
                break;
            case $typeLength == '5'://Four Photo change
                [$type , $oneType ,$twoType,$threeType,$fourType] = str_split($request->type,1); //12 [1,2]
                $new_replace_delLocal = $this->fourUploadImage($type,$oneType,$twoType,$threeType,$fourType,$request->file('photo'),$delLocal);
                break;
            default:
                break;
        }
        //cover photo image update  Type == 1 all upload Cover Photo
        if($request->type == '1'){
            if($cv =  $request->file('photo')){
                foreach($cv as $newCover){
                    $imageName = uniqid().$newCover->getClientOriginalName();
                    $newCover->move(public_path('/images/good/goodPhoto'),$imageName);
                    array_push( $coverImageName,$imageName);
                    $new_replace_delLocal = $coverImageName;
                }
                foreach($delLocal as $delCoverLocal)
                {
                    // return $delCoverLocal;
                    unlink(public_path("images/good/goodPhoto/".$delCoverLocal));
                }
            }
        };

        $updateData=[
            'name' => $request->name,
            'photo'=>json_encode($new_replace_delLocal),
            'shop_id'=> $request->shop_id,
            'price'=> $request->price,
            'discount'=> $request->discount,
            'description'=> $request->description,
            // 'good_spec_id'=> $request->good_spec_id,
            // 'good_para_id'=> $request->good_para_id,
            'category_id'=> $request->category_id
        ];
        return Good::find($id)->update($updateData);
    }

    public function uploadImage($type,$subType,$cv,$delLocal){
        switch ($type == '1') {
            case $subType == '0':
                    // local photo del
                    unlink(public_path("images/good/goodPhoto/".$delLocal[$subType]));
                    //Update upload sutyp 1
                    $imageName = uniqid().$cv->getClientOriginalName();
                    $cv->move(public_path('/images/good/goodPhoto'),$imageName);
                    $replacements = array(0 => $imageName);
                    $new_replace_delLocal = array_replace($delLocal,  $replacements );
                    return $new_replace_delLocal;
                break;
            case $subType == '1':
                // local photo del
                unlink(public_path("images/good/goodPhoto/".$delLocal[$subType]));
                //Update upload sutyp 1
                $imageName = uniqid().$cv->getClientOriginalName();
                $cv->move(public_path('/images/good/goodPhoto'),$imageName);
                $replacements = array(1 => $imageName);

                $new_replace_delLocal = array_replace($delLocal,  $replacements );
                return $new_replace_delLocal;
                break;
            case $subType == '2':
                // local photo del
                unlink(public_path("images/good/goodPhoto/".$delLocal[$subType]));
                //Update upload sutyp 1
                $imageName = uniqid().$cv->getClientOriginalName();
                $cv->move(public_path('/images/good/goodPhoto'),$imageName);
                $replacements = array(2 => $imageName);
                $new_replace_delLocal = array_replace($delLocal,  $replacements );
                return $new_replace_delLocal;
                break;
            case $subType == '3':
                 // local photo del
                 unlink(public_path("images/good/goodPhoto/".$delLocal[$subType]));
                 //Update upload sutyp 1
                 $imageName = uniqid().$cv->getClientOriginalName();
                 $cv->move(public_path('/images/good/goodPhoto'),$imageName);
                 $replacements = array(3 => $imageName);
                 $new_replace_delLocal = array_replace($delLocal,  $replacements );
                 return $new_replace_delLocal;
                break;
            case $subType == '4':
                // local photo del
                unlink(public_path("images/good/goodPhoto/".$delLocal[$subType]));
                //Update upload sutyp 1
                $imageName = uniqid().$cv->getClientOriginalName();
                $cv->move(public_path('/images/good/goodPhoto'),$imageName);
                $replacements = array(4 => $imageName);
                $new_replace_delLocal = array_replace($delLocal,  $replacements );
                return $new_replace_delLocal;
                break;
            default:

                break;
        }
    }
    public function twouploadImage($type,$oneType,$twoType,$cv,$delLocal){
        if($type == '1')
        {
                //Update upload one = 0 and two = 1
                $k = 0;
                $new_replace_delLocal = $delLocal;
                // return $new_replace_delLocal;
                foreach($cv as $c){
                    $imageName = uniqid().$c->getClientOriginalName();
                    $c->move(public_path('/images/good/goodPhoto'),$imageName);

                    //del old image
                    $oldImgOne=$new_replace_delLocal[$k];
                    unlink(public_path("images/good/goodPhoto/".$oldImgOne));

                    $new_replace_delLocal[$k] = $imageName;
                    $k++;
                }
                return $new_replace_delLocal;
        }
    }
    public function threeuploadImage($type,$oneType,$twoType,$threeType,$cv,$delLocal){
        if($type == '1')
        {
                //Update upload one = 0 and two = 1
                $k = 0;
                $new_replace_delLocal =$delLocal;
                foreach($cv as $c){
                    $imageName = uniqid().$c->getClientOriginalName();
                    $c->move(public_path('/images/good/goodPhoto'),$imageName);

                    //del old image
                    $oldImgOne=$new_replace_delLocal[$k];
                    unlink(public_path("images/good/goodPhoto/".$oldImgOne));

                    $new_replace_delLocal[$k] = $imageName;
                    $k++;

                }
                return $new_replace_delLocal;
        }
    }
    public function fouruploadImage($type,$oneType,$twoType,$threeType,$fourType,$cv,$delLocal){
        if($type == '1')
        {
                //Update upload one = 0 and two = 1
                $k = 0;
                $new_replace_delLocal =$delLocal;
                foreach($cv as $c){
                    $imageName = uniqid().$c->getClientOriginalName();
                    $c->move(public_path('/images/good/goodPhoto'),$imageName);

                    //del old image
                    $oldImgOne=$new_replace_delLocal[$k];
                    unlink(public_path("images/good/goodPhoto/".$oldImgOne));

                    $new_replace_delLocal[$k] = $imageName;
                    $k++;
                }
                return $new_replace_delLocal;
        }
    }

    public function search($request)
    {
        $name = $request->input('name');
        $good = Good::with('shop', 'category','good_specs','good_paras.currency')
        ->where('name', 'like', '%' . $name . '%')
        ->paginate(10);
        return $good;
    }
}

<?php
namespace App\Support;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
class LegacyContent
{
    private static function data(): object { return json_decode(file_get_contents(database_path('seed-data/legacy-content.json')), flags: JSON_THROW_ON_ERROR); }
    public static function categories(): Collection { return collect(self::data()->categories)->map(function($c){$c->_id=(string)$c->id;$c->slug=$c->slug ?: Str::slug($c->category_name);return $c;}); }
    private static function allPosts(): Collection { $cats=self::categories()->keyBy('id'); return collect(self::data()->posts)->map(function($p)use($cats){$p->_id=(string)$p->id;$p->views=0;$p->published_at=\Carbon\Carbon::parse($p->published_date ?: $p->created_date);$p->category=$cats->get($p->category_id);$p->author=(object)['name'=>'NewsHub'];return $p;}); }
    public static function posts(?string $search=null,?string $category=null): LengthAwarePaginator { $items=self::allPosts();if($search)$items=$items->filter(fn($p)=>Str::contains(Str::lower($p->title.' '.$p->content),Str::lower($search)));if($category)$items=$items->where('category_id',(int)$category);$page=LengthAwarePaginator::resolveCurrentPage();return new LengthAwarePaginator($items->forPage($page,9)->values(),$items->count(),9,$page,['path'=>request()->url(),'query'=>request()->query()]); }
    public static function post(string $id): ?object { return self::allPosts()->first(fn($p)=>(string)$p->_id===$id); }
}

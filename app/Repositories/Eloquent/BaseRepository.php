<?php


namespace App\Repositories\Eloquent;


use App\Exceptions\ModelNotDefined;
use App\Repositories\Contracts\IBase;
use App\Repositories\Criteria\ICriteria;
use Illuminate\Support\Arr;

abstract class BaseRepository implements IBase,ICriteria
{
    protected $model;

    public function __construct()
    {
        $this->model=$this->getModelClass();
    }

    public function withCriteria(...$criteria)
    {
        $criteria=Arr::flatten($criteria);
        foreach ($criteria as $criterion){
            $this->model=$criterion->apply($this->model);
        }
        return $this;
    }
    protected function getModelClass(){
        if (!method_exists($this,'model')){
            throw new ModelNotDefined();
        }
        return app()->make($this->model());
    }
    public function all(){
        return $this->model->get();
    }

    public function find($id){
        $result=$this->model->findOrFail($id);
        return $result;
    }

    public function findWhere($column,$value)
    {
        return $this->model->where($column,$value)->get();
    }

    public function findWhereFirst($column,$value)
    {
        return $this->model->where($column,$value)->firstOrFail();
    }

    public function paginate($perPage=10)
    {
        return $this->model->paginate($perPage);
    }

    public function create(array $data)
    {
        $result=$this->model->create($data);
        return $result;
    }
    public function update($id,array $data)
    {
        $record=$this->model->find($id);
        $record->update($data);
        return $record;
    }

    public function delete($id)
    {
        $record=$this->model->find($id);
        return $record->delete();
    }



}

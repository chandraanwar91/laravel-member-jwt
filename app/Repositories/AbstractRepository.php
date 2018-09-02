<?php namespace App\Repositories;

abstract class AbstractRepository {

    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;


    public function __construct($model)
    {
        $this->model = $model;
    }


    public function create(array $data)
    {
        return $this->model->create($data);
    }


    public function find($id, $columns = array('*'))
    {
        return $this->model->find($id, $columns);
    }


    public function all($columns = array('*'))
    {
        return $this->model->all($columns);
    }


    public function destroy($ids)
    {
        return $this->model->destroy(array($ids));
    }

}

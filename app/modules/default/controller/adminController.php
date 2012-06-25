<?php

class adminController extends LiController {

	private $config;
	
	protected $actions_with_layout = array(
		//'index' => 'base'
	);
	
	protected $filter = array(
		'before' => array(
		), 
		'after' => array(
		)
	);
	
	public function index() {
		
	}
	
	public function getDataGrid() {
	    
	    if (isset($this->params['model'])) {
	        $tabParams = explode('/', $this->params['model']);
	        
	        // Le grid d'une association
	        if (count($tabParams) == 3) {
                
	            // On envoi le nom de l'association, donc pas forcement celui de la classe ou de la table
	            //$model = $tabParams[2].'s';
	            $assoc_name = $tabParams[1];
	            // On a le nom de l'assoc, on cherche le nom de la classe liée à l'assoc
	            $from_model = $tabParams[0];
	            $fromModelObject = new $from_model;
	            $fromModelAssoc = $fromModelObject->getAssociations();
	            $model = $fromModelAssoc[$assoc_name]['class'];
	            $fk = $tabParams[2];
	            //$this->render_text('toto');return;
	            
	        //le grid du modèle
	        } else {
	            $model = $this->params['model'];
	        }
		    
		    // Création de l'objet qui sera renvoyé
		    $data = new stdClass();
    	    $data->metaData->totalProperty = 'results';
    	    $data->metaData->root = 'rows';
    	    $data->metaData->id = 'id';
    	    
    	    
		    
	        // Pas possible d'avoir un nom de classe Static dans une variable, donc bidouille
		    // Association
		    if (isset($fk)) {
		        eval('$this->modelDatasLimit = '.$from_model.'::get('.$fk.');');
		        $this->modelDatasLimit = $this->modelDatasLimit->$assoc_name;
		        
		        eval('$this->modelDatas = '.$from_model.'::get('.$fk.');');
		        $this->modelDatas = $this->modelDatas->$assoc_name;
		    // modele
		    } else {
		        eval('$this->modelDatasLimit = '.$model.'::find();');
		        eval('$this->modelDatas = '.$model.'::find();');
		    }
		    
		    if ( (isset($this->params['limit'])) && ($this->params['limit'] != 0) ) {
		    	$this->modelDatasLimit = $this->modelDatasLimit->limit($this->params['limit'], $this->params['start']);
		    }
		    if (isset($this->params['sort'])) {
		    	$this->modelDatasLimit = $this->modelDatasLimit->order_by($this->params['sort'].' '.$this->params['dir']);
		    	$data->metaData->sortInfo = new stdClass();
	    	    $data->metaData->sortInfo->field = $this->params['sort'];
	    	    $data->metaData->sortInfo->direction = $this->params['dir'];
	    	    
	    	    //$data->metaData->sortInfo = $sortInfo;
		    } else {
		    	$data->metaData->sortInfo = new stdClass();
	    	    $data->metaData->sortInfo->field = 'id';
	    	    $data->metaData->sortInfo->direction = 'asc';
		    }
		    
		    
    	    
    	    // Les meta données
    	    $modelSample = new $model;
    	    $model_columns = $modelSample->query_columns();
    	    $columns = array();
    	    $fields = array();
    	    foreach ($model_columns as $column) {
    	        $col = new stdClass();
    	        $col->dataIndex = $column[0];
    	        $col->header = ucwords($column[0]);
    	        $col->sortable = true;
    	        
    	        $field = new stdClass();
    	        $field->name = $column[0];
    	        
    	        $fields[] = $field;
    	        $columns[] = $col;
    	    }
    	    $associations = $modelSample->getAssociations();
    	    $tabAssociation = array();
    	    foreach ($associations as $key => $association) {
    	        $association['model'] = $key;
    	        $tabAssociation[] = $association;
    	    }
    	    $data->metaData->associations = $tabAssociation;
    	    
    	    $data->metaData->fields = $fields;
    	    $data->metaData->columns = $columns;
    	    
    	    // Les données
    	    $rows = array();
    	    foreach ($this->modelDatasLimit as $rowData) {
    	        $d = new stdClass();
    	        foreach ($model_columns as $column) {
    	            $d->{$column[0]} = $rowData->{$column[0]};
    	        }
    	        $rows[] = $d;
    	    }
    	    $data->rows = $rows;
	        $data->results = $this->modelDatas->count();//count($rows);
	    
	        $this->render_text(json_encode($data));
	    }
	}
	
	
	public function getModelList() {
        
		$models = array();
	    
        $classes =LiBootModel::getModels();
        foreach ($classes as $class) {
            $modelObject = new stdClass();
            $modelObject->id = ucwords($class);
            $modelObject->text = ucwords($class);
            $modelObject->cls = 'file';
            $modelObject->leaf = true;
            
            $models[] = $modelObject;
		}

		$this->render_text( json_encode($models) );
	}
}
?>

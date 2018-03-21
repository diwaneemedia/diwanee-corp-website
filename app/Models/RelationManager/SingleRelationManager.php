<?php
namespace App\Models\RelationManager;

class SingleRelationManager extends RelationManager {
    protected function getAllRelationItemsQuery($relationsSettings) {
        if(isset($relationsSettings['pivotModel'])) {
            $query = $this->object->belongsToMany($relationsSettings['model'], $relationsSettings['pivot'])->using($relationsSettings['pivotModel']);
        } else {
            $query = $this->object->belongsToMany($relationsSettings['model'], $relationsSettings['pivot'], $relationsSettings['foreignKey'], $relationsSettings['relationKey']);
        }
        
        return $query;
    }
    
    public function saveRelation($data) {
        $this->populateRelationData($data);
        
        if(!empty($this->relationData)) {
            $relationItemId = array_keys($this->relationData)[0];
            $relationItemData = $this->relationData[$relationItemId];

            $relation = $this->relation;

            if(isset($this->object->$relation) && $this->object->$relation->id != $relationItemId) {
                $this->object->$relation()->detach($this->object->$relation->id);
            }

            if($relationItemId > 0) {
                $this->attach($relation, $relationItemId, $relationItemData);
            }
        }
    }
    
    protected function populateRelationData($data) {
        $this->relationData = [];
        
        $relation = $this->relation;
        if(isset($data[$relation])) {
            $this->relationData[$data[$relation][0]] = [];
            if(isset($data['relation_items'][$relation])) {
                $this->relationData = $data['relation_items'][$relation];
            }
        }
    }

    protected function attach($relation, $relationItemId, $relationItemData) {
        if($this->object->sortableField($relation) !== null) {
            $relationItemData[$this->object->sortableField($relation)] = 0;
        }
        
        if(!isset($this->object->$relation) || $this->object->$relation->id != $relationItemId) {
            $this->object->$relation()->detach();
            $this->object->$relation()->attach([$relationItemId => $relationItemData]);
        }
    }
}
<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Constants\Models;
use App\Constants\FieldTypeCategory;
use App\Models\Node\NodeModelDBGenerator;
use App\Models\Node\ClassGenerator\ClassGenerator;
use App\Constants\Settings;
use App\Utils\Utils;

class NodeType extends AppModel {
    use SoftDeletes;

    protected $fillable = ['name'];

    protected $allAttributesFields = ['id', 'name', 'created_at', 'updated_at', 'deleted_at'];
    
    protected $allFieldsFromPivots = [];

    protected $requiredFields = ['name'];
    
    protected $defaultDropdownColumn = 'name';

    protected $attributeType = [
        'status' => Models::AttributeType_Enum
    ];

    protected $relationsSettings = [
        'fields' => [
            'relationType' => 'belongsToMany',
            'model' => 'App\\Field',
            'pivot' => 'node_type_field',
            'pivotModel' => 'App\\NodeTypeField',
            'filters' => ['field_type.category' => [FieldTypeCategory::Attribute]],
            'sortBy' => 'ordinal_number',
            'extraFields' => ['active', 'required'],
            'automaticSave' => false
        ],
        'attribute_fields' => [
            'parent' => 'fields',
            'filters' => ['field_type.category' => [FieldTypeCategory::Attribute]],
            'automaticSave' => true
        ],
        'tag_fields' => [
            'parent' => 'fields',
            'filters' => ['field_type.category' => [FieldTypeCategory::Tag]],
            'extraFields' => ['active', 'required', 'multiple_list'],
            'automaticSave' => true
        ],
        'sir_trevor_fields' => [
            'parent' => 'fields',
            'filters' => ['field_type.category' => [FieldTypeCategory::SirTrevor]],
            'automaticSave' => true
        ],

        'nodes' => [
            'relationType' => 'hasMany',
            'model' => 'App\\Node',
            'foreignKey' => 'node_type_id',
            'relationKey' => 'id',
            'automaticSave' => false
        ]
    ];
    
    protected $multipleFields = [
        'fields' => true,
        'attribute_fields' => true,
        'tag_fields' => true,
        'sir_trevor_fields' => true,
        'nodes' => true
    ];

    protected $dependsOn = [];
    
    public function getAdditionalDataTableNameAttribute() {
        return empty($this->name) ? $this->name : Settings::NodeModelPrefix . '_' . Utils::getFormattedDBName($this->name) . 's';
    }

    public function saveData(array $data) {
        $oldName = $this->name;
        $oldAdditionalDataTableName = $this->additionalDataTableName;

        parent::saveData($data);
        
        $dbGenerator = new NodeModelDBGenerator($this, $oldAdditionalDataTableName);
        $dbGenerator->generate();

        ClassGenerator::generateAllFilesForNodeType($this, $oldName);
    }

    public function getSTFieldsArray() {
        $stFields = array();
        $sirTrevorRelationName = FieldTypeCategory::SirTrevor . '_fields';
        foreach($this->$sirTrevorRelationName as $field) {
            $stFields[] = str_replace(' ', '', $field->title);
        }
        return $stFields;
    }

    public function getRequiredSTFieldsArray() {
        $reqFields = array();
        $sirTrevorRelationName = FieldTypeCategory::SirTrevor . '_fields';
        foreach($this->$sirTrevorRelationName as $field) {
            if($field->pivot->required) {
                $reqFields[] = str_replace(' ', '', $field->title);
            }
        }
        return $reqFields;
    }
    
    protected function checkIfCanRemoveRelationItem($relation) {
        if(strpos($relation, 'fields') !== false) {
            return (count($this->nodes) === 0);
        }
        
        return true;
    }
}
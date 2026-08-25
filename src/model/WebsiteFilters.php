<?php

namespace RR\model;

use RR\core\Model;
use RR\libs\Util;

class WebsiteFilters extends Model
{
    private $table;

    function __construct()
    {
        $this->table = 'website_filters';
        $joins = [];

        parent::__construct($this->table, $joins);
    }

    public function submitEditFilters()
    {
        $arrFilters = [
            '1' => (object)[
                'id' => 1,
                'name' => 'Tipo',
                'slugify' => 'tipo',
                'placeholder' => 'Tipo',
            ],
            '2' => (object)[
                'id' => 2,
                'name' => 'Categoria',
                'slugify' => 'categoria',
                'placeholder' => 'Categoria',
            ],
            '3' => (object)[
                'id' => 3,
                'name' => 'Cidade',
                'slugify' => 'cidade',
                'placeholder' => 'Cidade',
            ],
            '4' => (object)[
                'id' => 4,
                'name' => 'Bairro',
                'slugify' => 'bairro',
                'placeholder' => 'Bairro',
            ],
            '5' => (object)[
                'id' => 5,
                'name' => 'Nome',
                'slugify' => 'nome',
                'placeholder' => 'Código, Desc, Nome, Rua...',
            ],
            '6' => (object)[
                'id' => 6,
                'name' => 'Valor de',
                'slugify' => 'valor_minimo',
                'placeholder' => 'de',
            ],
            '7' => (object)[
                'id' => 7,
                'name' => 'Valor até',
                'slugify' => 'valor_maximo',
                'placeholder' => 'ate',
            ]
        ];

        $lastId = count($arrFilters) + 1;

        array_map(function ($resource) use (&$arrFilters, &$lastId) {
            $arrFilters[$lastId] = (object)[
                'id' => $lastId,
                'name' => $resource->name,
                'slugify' => $resource->slugify,
                'placeholder' => '',
            ];
            $lastId++;
        }, (new ImmovableResource)->getWithFiltersAllItems([(object)['columns' => ['status' => (object)['value' => 1], 'site_filter' => (object)['value' => 1]]]])->data);

        $response = [];
        try {
            $this->db->beginTransaction();

            foreach ($_POST as $key => $value) {
                $filter = array_filter($arrFilters, function ($filter) use ($value) {
                    return $filter->id == $value;
                });
                $filter = array_values($filter)[0];

                $response[] = $this->update([
                    'name' => $filter->name,
                    'slugify' => $filter->slugify,
                    'placeholder' => $filter->placeholder,
                    'item_order' => $key,
                    'id_item' => $value
                ], 'id', $key);
            }

            (new SettingsSite)->update(['advanced_filter' => $_POST['advanced_filter'], 'input_type' => $_POST['input_type']], 'id', 1);
            $this->db->commit();
        } catch (\PDOException $error) {
            $this->db->rollBack();
            if (ENVIRONMENT) {
                echo $error->getMessage();
            }
        }
        $response = array_filter($response, function ($item) {
            return $item->error == true;
        });

        return (object)[
            'error' => !empty($response) ? true : false,
            'message' => !empty($response) ? 'Erro ao salvar filtros' : 'Filtro salvo com sucesso'
        ];
    }
}

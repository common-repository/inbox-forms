<?php

namespace Inbox\Repositories;

class FormRepository extends AbstractRepository
{
    const TYPE_POPUP = 0;

    const TYPE_INLINE = 1;

    const RULE_TYPE_CONTAIN = 0;

    const RULE_TYPE_EQUAL = 1;

    const RULE_TYPE_NOT_CONTAIN = 2;

    const RULE_TYPE_NOT_EQUAL = 3;

    /**
     * @param string $type
     *
     * @return string
     */
    public static function getTypeName($type)
    {
        switch ($type) {
            case self::TYPE_POPUP:
                $value = __('Popup Form', 'inbox');
                break;
            case self::TYPE_INLINE:
                $value = __('Inline Form', 'inbox');
                break;
            default:
                $value = ucwords($type);
        }

        return $value;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public static function getRuleTypeName($type)
    {
        switch ($type) {
            case self::RULE_TYPE_CONTAIN:
                $value = __('Contains', 'inbox');
                break;
            case self::RULE_TYPE_EQUAL:
                $value = __('Equals', 'inbox');
                break;
            case self::RULE_TYPE_NOT_CONTAIN:
                $value = __('Not Contains', 'inbox');
                break;
            case self::RULE_TYPE_NOT_EQUAL:
                $value = __('Not Equals', 'inbox');
                break;
            default:
                $value = ucwords($type);
        }

        return $value;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public static function formExistsByName($name)
    {
        $formName = sanitize_text_field($name);
        $table = parent::forms_table();
        $result = parent::wpdb()->get_var(
            parent::wpdb()->prepare("SELECT name FROM $table WHERE name = '%s'", $formName)
        );

        return ! empty($result);
    }

    /**
     * @return int
     */
    public static function formCount()
    {
        $table = parent::forms_table();

        return absint(parent::wpdb()->get_var("SELECT COUNT(*) FROM $table"));
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public static function getFormName($id)
    {
        $table = parent::forms_table();

        return parent::wpdb()->get_var(
            parent::wpdb()->prepare("SELECT name FROM $table WHERE id = %d", $id)
        );
    }

    /**
     * @return array
     */
    public static function getForms()
    {
        $table = parent::forms_table();

        return parent::wpdb()->get_results("SELECT * FROM $table", 'ARRAY_A');
    }

    /**
     * @return array
     */
    public static function getAutoForms()
    {
        $table = parent::forms_table();

        return parent::wpdb()->get_results("SELECT * FROM $table WHERE is_active = 1 and rules is not null and rules != '[]'", 'ARRAY_A');
    }

    /**
     * @param int $id
     *
     * @return mixed
     */
    public static function getForm($id)
    {
        $table = parent::forms_table();

        return parent::wpdb()->get_row(
            parent::wpdb()->prepare("SELECT * FROM $table WHERE id = %d", $id),
            'ARRAY_A'
        );
    }

    /**
     * @param array $data
     *
     * @return false|int
     */
    public static function createForm(array $data)
    {
        $response = parent::wpdb()->insert(
            parent::forms_table(),
            [
                'name' => stripslashes_deep($data['name']),
                'form_id' => stripslashes_deep($data['form_id']),
                'url' => stripslashes_deep($data['url']),
                'type' => absint($data['type']),
                'selector' => stripslashes_deep($data['selector'] ?? null),
                'timeout' => isset($data['timeout']) ? absint($data['timeout']) : null,
                'days' => isset($data['days']) ? absint($data['days']) : null,
                'rules' => json_encode($data['rules'] ?? []),
                'is_active' => (bool) ($data['is_active'] ?? false)
            ],
            ['%s', '%s', '%s', '%d', '%s', '%d', '%d', '%s', '%d']
        );

        return ! $response ? $response : parent::wpdb()->insert_id;
    }

    /**
     * @param int $id
     * @param array $data
     *
     * @return false|int
     */
    public static function updateForm($id, array $data)
    {
        $table = parent::forms_table();

        return parent::wpdb()->update(
            $table,
            [
                'name' => stripslashes_deep($data['name']),
                'form_id' => stripslashes_deep($data['form_id']),
                'url' => stripslashes_deep($data['url']),
                'type' => absint($data['type']),
                'selector' => stripslashes_deep($data['selector'] ?? null),
                'timeout' => isset($data['timeout']) ? absint($data['timeout']) : null,
                'days' => isset($data['days']) ? absint($data['days']) : null,
                'rules' => json_encode($data['rules'] ?? []),
                'is_active' => (bool) ($data['is_active'] ?? false)
            ],
            ['id' => absint($id)],
            ['%s', '%s', '%s', '%d', '%s', '%d', '%d', '%s', '%d'],
            ['%d']
        );
    }

    /**
     * @param int $id
     *
     * @return false|int
     */
    public static function deleteForm($id)
    {
        $table = parent::forms_table();

        return parent::wpdb()->delete(
            $table,
            ['id' => $id],
            ['%d']
        );
    }
}

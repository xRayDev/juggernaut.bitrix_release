<?php

namespace Jugger\Db\Orm\D7;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

/**
 * Class ElementPropertyTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> IBLOCK_PROPERTY_ID int mandatory
 * <li> IBLOCK_ELEMENT_ID int mandatory
 * <li> VALUE string mandatory
 * <li> VALUE_TYPE enum ('text', 'html') optional default 'text'
 * <li> VALUE_ENUM int optional
 * <li> VALUE_NUM double optional
 * <li> DESCRIPTION string(255) optional
 * <li> IBLOCK_ELEMENT reference to {@link \Bitrix\Iblock\IblockElementTable}
 * <li> IBLOCK_PROPERTY reference to {@link \Bitrix\Iblock\IblockPropertyTable}
 * </ul>
 *
 * @package Bitrix\Iblock
 **/

class ElementPropertyTable extends DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_iblock_element_property';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'ID' => new Entity\IntegerField('ID', array(
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_ID_FIELD'),
			)),
			'IBLOCK_PROPERTY_ID' => new Entity\IntegerField('IBLOCK_PROPERTY_ID', array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_IBLOCK_PROPERTY_ID_FIELD'),
			)),
			'IBLOCK_ELEMENT_ID' => new Entity\IntegerField('IBLOCK_ELEMENT_ID', array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_IBLOCK_ELEMENT_ID_FIELD'),
			)),
			'VALUE' => new Entity\TextField('VALUE', array(
				'required' => true,
				'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_VALUE_FIELD'),
			)),
			'VALUE_TYPE' => new Entity\EnumField('VALUE_TYPE', array(
				'values' => array('text', 'html'),
				'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_VALUE_TYPE_FIELD'),
			)),
			'VALUE_ENUM' => new Entity\IntegerField('VALUE_ENUM', array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_VALUE_ENUM_FIELD'),
			)),
			'VALUE_NUM' => new Entity\FloatField('VALUE_NUM', array(
				'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_VALUE_NUM_FIELD'),
			)),
			'DESCRIPTION' => new Entity\StringField('DESCRIPTION', array(
				'validation' => array(__CLASS__, 'validateDescription'),
				'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_DESCRIPTION_FIELD'),
			))
		);
	}
	/**
	 * Returns validators for DESCRIPTION field.
	 *
	 * @return array
	 */
	public static function validateDescription()
	{
		return array(
			new Main\Entity\Validator\Length(null, 255),
		);
	}
}
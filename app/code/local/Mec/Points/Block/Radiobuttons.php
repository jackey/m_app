<?php

/**
 * Radio Buttons 
 */
class Mec_Points_Block_Radiobuttons extends Mage_Core_Block_Html_Select {
    /**
     * Render HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_beforeToHtml()) {
            return '';
        }
        $values = $this->getValue();
        //$html = '<select name="' . $this->getName() . '" id="' . $this->getId() . '" class="'
        //    . $this->getClass() . '" title="' . $this->getTitle() . '" ' . $this->getExtraParams() . '>';
        $html = '';

        $isArrayOption = true;
        foreach ($this->getOptions() as $key => $option) {
            if ($isArrayOption && is_array($option)) {
                $value  = $option['value'];
                $label  = (string)$option['label'];
                $params = (!empty($option['params'])) ? $option['params'] : array();
            } else {
	            $value = (string)$key;
	            $label = (string)$option;
	            $isArrayOption = false;
	            $params = array();
            }


            $html .= $this->_optionToHtml(
                array(
                    'value' => $value,
                    'label' => $label,
                    'params' => $params
                ),
                in_array($value, $values)
            );
        }
        return $html;
    }

    /**
     * Return option HTML node
     *
     * @param array $option
     * @param boolean $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected = false)
    {
        $selectedHtml = $selected ? ' selected="selected"' : '';
        if ($this->getIsRenderToJsTemplate() === true) {
            $selectedHtml .= ' #{option_extra_attr_' . self::calcOptionHash($option['value']) . '}';
        }

        $params = '';
        if (!empty($option['params']) && is_array($option['params'])) {
            foreach ($option['params'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $keyMulti => $valueMulti) {
                        $params .= sprintf(' %s="%s" ', $keyMulti, $valueMulti);
                    }
                } else {
                    $params .= sprintf(' %s="%s" ', $key, $value);
                }
            }
        }

        return sprintf('<input type="radio" class="%s" id="%s" name="%s" value="%s"%s %s>%s</option>',
			$this->getClass(),
			$this->getId(),
        	$this->getName(),
            $this->escapeHtml($option['value']),
            $selectedHtml,
            $params,
            $this->escapeHtml($option['label']));
    }
}
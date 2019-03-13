<?php

class BackLib_View_Helper_FilterForm
    extends Zend_View_Helper_Abstract
{
    public function filterForm($values, array $fields_spec)
    {
        $html  = "<fieldset><legend>" . BackLib_Lang::tr("legend_filter_form") . "</legend>";
        $html .= "<form action='' name='filter-form' method='post'>";
        $html .= "<table class='filter-table' cellpadding='0' cellspacing='0' border='0px'><tbody>";

        $html .= $this->_getFieldsHTML($values, $fields_spec);

        $html .= "</tbody></table>";
        $html .= "<br /><input type='submit' class='button' name='' value='" . BackLib_Lang::tr("filter_submit") . "' />";
        $html .= "<button class='button' id='filter-reset'>" . BackLib_Lang::tr("filter_reset") . "</button>";
        /* XXX clean filter button */
        $html .= "</form>";
        $html .= "</fieldset>";

        $html .= $this->_getResetJavascript();

        return $html;
    }

    protected function _getFieldsHTML(array $values, array $fields_spec)
    {
        $html = "";

        $i = 0;
        foreach ($fields_spec as $field_name => $spec) {
            if (! isset($spec["label"]))
                continue;

            if ($i % 2 == 0)
                $html .= "<tr>";

            $name    = "filter[{$field_name}]";
            $label   = $spec["label"];
            $type    = isset($spec["type"]) ? $spec["type"] : "text";
            $value   = isset($spec["value"]) ? $spec["value"] : null;
            $options = isset($spec["options"]) ? $spec["options"] : null;
            $attribs = isset($spec["attribs"]) ? $spec["attribs"] : null;

            if (isset($values[$field_name]))
                $value = $values[$field_name];

            $html .= "<td>";
            $html .= "<label for='{$name}'>{$label}</label>";
            switch ($type) {
                case "text":
                    $html .= $this->view->formText($name, $value, $attribs);
                    break;

                case "select":
                    $html .= $this->view->formSelect($name, $value, $attribs, $options);
                    break;

                case "checkbox":
                    $html .= $this->view->formCheckbox($name, $value, $attribs);
                    break;
            }
            $html .= "</td>";

            if (($i+1) % 2 == 0)
                $html .= "</tr>";

            $i++;
        }

        return $html;
    }

    protected function _getResetJavascript()
    {
        return '
        <script type="text/javascript">
            var resetFields = function(evt)
            {
                $("form[name=\'filter-form\'] input[type=\'text\']").each(function(elm) {
                    this.value = "";
                });

                $("form[name=\'filter-form\'] select option[value=\'\']").each(function(elm) {
                    $(this).attr("selected", "selected");

                    $("form[name=\'filter-form\'] select option[value!=\'\']").each(function(elm2) {
                        $(this).removeAttr("selected");
                    });
                });

                /* XXX type checkbox */
            };

            $("form[name=\'filter-form\'] button#filter-reset").each(function(elm) {
                this.addEventListener("click", resetFields, false);
            });
        </script>
        ';
    }

    protected function _getInitJavascript()
    {
        return '
        <script type="text/javascript">
        $(document).ready(function(evt) {
            $.eRestaurant.initialize();
        });
        </script>
        ';
    }
}


-- Quantity & placeholder flags only on native languange
UPDATE `{{TABLE}}`
   SET `quantity` = 0
     , `var` = 0
 WHERE `lang` != '{{NATIVE}}';

-- Reset last changed timestamp
UPDATE `{{TABLE}}`
   SET `changed` = 0;

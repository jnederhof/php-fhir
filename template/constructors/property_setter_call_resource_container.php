<?php

/*
 * Copyright 2016-2019 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

use DCarbone\PHPFHIR\Enum\TypeKindEnum;

/** @var \DCarbone\PHPFHIR\Definition\Type $type */
/** @var \DCarbone\PHPFHIR\Definition\Property $property */

$isCollection = $property->isCollection();
$propertyName = $property->getName();
$propertyFieldConst = $property->getFieldConstantName();
$propertyType = $property->getValueFHIRType();
$propertyTypeKind = $propertyType->getKind();
$propertyTypeClassName = $propertyType->getClassName();
$setter = ($isCollection ? 'add' : 'set') . ucfirst($propertyName);

ob_start(); ?>
        if (isset($data[self::<?php echo $propertyFieldConst; ?>])) {
<?php if ($isCollection) : ?>
            if (is_array($data[self::<?php echo $propertyFieldConst; ?>])) {
                foreach($data[self::<?php echo $propertyFieldConst; ?>] as $v) {
                    if (null === $v) {
                        continue;
                    }
                    if (is_object($v)) {
                        if ($v instanceof PHPFHIRContainedTypeInterface) {
                            $this-><?php echo $setter; ?>($v);
                        } else {
                            throw new \InvalidArgumentException(sprintf(
                                '<?php echo $type->getClassName(); ?> - Field "<?php echo $propertyName; ?>" must be an array of objects implementing PHPFHIRContainedTypeInterface, object of type %s seen',
                                get_class($v)
                            ));
                        }
                    } else if (is_array($v)) {
                        $typeClass = PHPFHIRTypeMap::getContainedTypeFromArray($v);
                        if (null === $typeClass) {
                            throw new \InvalidArgumentException(sprintf(
                                '<?php echo $type->getClassName(); ?> - Unable to determine class for field "<?php echo $propertyName; ?>" from value: %s',
                                json_encode($v)
                            ));
                        }
                        $this-><?php echo $setter; ?>(new $typeClass($v));
                    } else {
                        throw new \InvalidArgumentException(sprintf(
                            '<?php echo $type->getClassName(); ?> - Unable to determine class for field "<?php echo $propertyName; ?>" from value: %s',
                            json_encode($v)
                        ));
                    }
                }
            } else if ($data[self::<?php echo $propertyFieldConst; ?>] instanceof PHPFHIRContainedTypeInterface) {
                $this-><?php echo $setter; ?>($data[self::<?php echo $propertyFieldConst; ?>]);
            } else {
                $typeClass = PHPFHIRTypeMap::getContainedTypeFromArray($data[self::<?php echo $propertyFieldConst; ?>]);
                if (null === $typeClass) {
                    throw new \InvalidArgumentException(sprintf(
                        '<?php echo $type->getClassName(); ?> - Unable to determine class for field "<?php echo $propertyName; ?>" from value: %s',
                        json_encode($data[self::<?php echo $propertyFieldConst; ?>])
                    ));
                }
                $this-><?php echo $setter; ?>(new $typeClass($data[self::<?php echo $propertyFieldConst; ?>]));
            }
<?php else : ?>
            if (is_object($data[self::<?php echo $propertyFieldConst; ?>])) {
                if ($data[self::<?php echo $propertyFieldConst; ?>] instanceof PHPFHIRContainedTypeInterface) {
                    $this-><?php echo $setter; ?>($data[self::<?php echo $propertyFieldConst; ?>]);
                } else {
                    throw new \InvalidArgumentException(sprintf(
                        '<?php echo $type->getClassName(); ?> - Field "<?php echo $propertyName; ?>" must be an object implementing PHPFHIRContainedTypeInterface, object of type %s seen',
                        get_class($data[self::<?php echo $propertyFieldConst; ?>])
                    ));
                }
            } else if (is_array($data[self::<?php echo $propertyFieldConst; ?>])) {
                $typeClass = PHPFHIRTypeMap::getContainedTypeFromArray($data[self::<?php echo $propertyFieldConst; ?>]);
                if (null === $typeClass) {
                    throw new \InvalidArgumentException(sprintf(
                        '<?php echo $type->getClassName(); ?> - Unable to determine class for field "<?php echo $propertyName; ?>" from value: %s',
                        json_encode($data[self::<?php echo $propertyFieldConst; ?>])
                    ));
                }
                $this-><?php echo $setter; ?>(new $typeClass($data[self::<?php echo $propertyFieldConst; ?>]));
            } else {
                throw new \InvalidArgumentException(sprintf(
                    '<?php echo $type->getClassName(); ?> - Unable to determine class for field "<?php echo $propertyName; ?>" from value: %s',
                    json_encode($data[self::<?php echo $propertyFieldConst; ?>])
                ));
            }
<?php endif; ?>
        }
<?php
return ob_get_clean();

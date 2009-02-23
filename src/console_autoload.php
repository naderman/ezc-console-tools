<?php
/**
 * Autoloader definition for the ConsoleTools component.
 *
 * @copyright Copyright (C) 2005-2009 eZ Systems AS. All rights reserved.
 * @license http://ez.no/licenses/new_bsd New BSD License
 * @version //autogentag//
 * @filesource
 * @package ConsoleTools
 */

return array(
    'ezcConsoleException'                           => 'ConsoleTools/exceptions/exception.php',
    'ezcConsoleArgumentException'                   => 'ConsoleTools/exceptions/argument.php',
    'ezcConsoleOptionException'                     => 'ConsoleTools/exceptions/option.php',
    'ezcConsoleArgumentAlreadyRegisteredException'  => 'ConsoleTools/exceptions/argument_already_registered.php',
    'ezcConsoleArgumentMandatoryViolationException' => 'ConsoleTools/exceptions/argument_mandatory_violation.php',
    'ezcConsoleArgumentTypeViolationException'      => 'ConsoleTools/exceptions/argument_type_violation.php',
    'ezcConsoleDialogAbortException'                => 'ConsoleTools/exceptions/dialog_abort.php',
    'ezcConsoleInvalidOptionNameException'          => 'ConsoleTools/exceptions/invalid_option_name.php',
    'ezcConsoleInvalidOutputTargetException'        => 'ConsoleTools/exceptions/invalid_output_target.php',
    'ezcConsoleNoPositionStoredException'           => 'ConsoleTools/exceptions/no_position_stored.php',
    'ezcConsoleNoValidDialogResultException'        => 'ConsoleTools/exceptions/no_valid_dialog_result.php',
    'ezcConsoleOptionAlreadyRegisteredException'    => 'ConsoleTools/exceptions/option_already_registered.php',
    'ezcConsoleOptionArgumentsViolationException'   => 'ConsoleTools/exceptions/option_arguments_violation.php',
    'ezcConsoleOptionDependencyViolationException'  => 'ConsoleTools/exceptions/option_dependency_violation.php',
    'ezcConsoleOptionExclusionViolationException'   => 'ConsoleTools/exceptions/option_exclusion_violation.php',
    'ezcConsoleOptionMandatoryViolationException'   => 'ConsoleTools/exceptions/option_mandatory_violation.php',
    'ezcConsoleOptionMissingValueException'         => 'ConsoleTools/exceptions/option_missing_value.php',
    'ezcConsoleOptionNoAliasException'              => 'ConsoleTools/exceptions/option_no_alias.php',
    'ezcConsoleOptionNotExistsException'            => 'ConsoleTools/exceptions/option_not_exists.php',
    'ezcConsoleOptionStringNotWellformedException'  => 'ConsoleTools/exceptions/option_string_not_wellformed.php',
    'ezcConsoleOptionTooManyValuesException'        => 'ConsoleTools/exceptions/option_too_many_values.php',
    'ezcConsoleOptionTypeViolationException'        => 'ConsoleTools/exceptions/option_type_violation.php',
    'ezcConsoleTooManyArgumentsException'           => 'ConsoleTools/exceptions/argument_too_many.php',
    'ezcConsoleDialogValidator'                     => 'ConsoleTools/interfaces/dialog_validator.php',
    'ezcConsoleQuestionDialogValidator'             => 'ConsoleTools/interfaces/question_dialog_validator.php',
    'ezcConsoleDialog'                              => 'ConsoleTools/interfaces/dialog.php',
    'ezcConsoleDialogOptions'                       => 'ConsoleTools/options/dialog.php',
    'ezcConsoleMenuDialogValidator'                 => 'ConsoleTools/interfaces/menu_dialog_validator.php',
    'ezcConsoleQuestionDialogCollectionValidator'   => 'ConsoleTools/dialog/validators/question_dialog_collection.php',
    'ezcConsoleArgument'                            => 'ConsoleTools/input/argument.php',
    'ezcConsoleArguments'                           => 'ConsoleTools/input/arguments.php',
    'ezcConsoleDialogViewer'                        => 'ConsoleTools/dialog_viewer.php',
    'ezcConsoleInput'                               => 'ConsoleTools/input.php',
    'ezcConsoleMenuDialog'                          => 'ConsoleTools/dialog/menu_dialog.php',
    'ezcConsoleMenuDialogDefaultValidator'          => 'ConsoleTools/dialog/validators/menu_dialog_default.php',
    'ezcConsoleMenuDialogOptions'                   => 'ConsoleTools/options/menu_dialog.php',
    'ezcConsoleOption'                              => 'ConsoleTools/input/option.php',
    'ezcConsoleOptionRule'                          => 'ConsoleTools/structs/option_rule.php',
    'ezcConsoleOutput'                              => 'ConsoleTools/output.php',
    'ezcConsoleOutputFormat'                        => 'ConsoleTools/structs/output_format.php',
    'ezcConsoleOutputFormats'                       => 'ConsoleTools/structs/output_formats.php',
    'ezcConsoleOutputOptions'                       => 'ConsoleTools/options/output.php',
    'ezcConsoleProgressMonitor'                     => 'ConsoleTools/progressmonitor.php',
    'ezcConsoleProgressMonitorOptions'              => 'ConsoleTools/options/progressmonitor.php',
    'ezcConsoleProgressbar'                         => 'ConsoleTools/progressbar.php',
    'ezcConsoleProgressbarOptions'                  => 'ConsoleTools/options/progressbar.php',
    'ezcConsoleQuestionDialog'                      => 'ConsoleTools/dialog/question_dialog.php',
    'ezcConsoleQuestionDialogMappingValidator'      => 'ConsoleTools/dialog/validators/question_dialog_mapping.php',
    'ezcConsoleQuestionDialogOptions'               => 'ConsoleTools/options/question_dialog.php',
    'ezcConsoleQuestionDialogRegexValidator'        => 'ConsoleTools/dialog/validators/question_dialog_regex.php',
    'ezcConsoleQuestionDialogTypeValidator'         => 'ConsoleTools/dialog/validators/question_dialog_type.php',
    'ezcConsoleStatusbar'                           => 'ConsoleTools/statusbar.php',
    'ezcConsoleStatusbarOptions'                    => 'ConsoleTools/options/statusbar.php',
    'ezcConsoleTable'                               => 'ConsoleTools/table.php',
    'ezcConsoleTableCell'                           => 'ConsoleTools/table/cell.php',
    'ezcConsoleTableOptions'                        => 'ConsoleTools/options/table.php',
    'ezcConsoleTableRow'                            => 'ConsoleTools/table/row.php',
);
?>

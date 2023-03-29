# Acceptance Test Report

## Automatic Html Encoding (Phpolar\Phpolar\AutomaticHtmlEncoding)
- [x] Should prevent cross-site scripting injection

## Configurable Form Field (Phpolar\Phpolar\ConfigurableFormField)
- [x] Shall support configurable form validation for Phpolar\Phpolar\Validation\Max
- [x] Shall support configurable form validation for Phpolar\Phpolar\Validation\MaxLength
- [x] Shall support configurable form validation for Phpolar\Phpolar\Validation\Min
- [x] Shall support configurable form validation for Phpolar\Phpolar\Validation\MinLength
- [x] Shall support configurable form validation for Phpolar\Phpolar\Validation\Pattern
- [x] Shall support configurable form validation alerts with message: "Value is greater than the maximum"
- [x] Shall support configurable form validation alerts with message: "Maximum length validation failed"
- [x] Shall support configurable form validation alerts with message: "Value is less than the minimum"
- [x] Shall support configurable form validation alerts with message: "Minimum length validation failed"
- [x] Shall support configurable form validation alerts with message: "Pattern validation failed"
- [x] Shall support configurable form validation alerts with message: "Required value"
- [x] Shall support converting detected datetime-local input types to string
- [x] Shall support converting detected number input types to string
- [x] Shall support converting detected checkbox input types to string
- [x] Shall support converting detected text input types to string
- [x] Shall support configurable form labels
- [x] Shall support form field type detection
- [x] Shall support hidden form field configuration

## Configurable Storage Entry (Phpolar\Phpolar\ConfigurableStorageEntry)
- [x] Should configure column names
- [x] Should detect data types
- [x] Should allow configuration of size
- [x] Should have optional table name configuration

## Memory Usage (Phpolar\Phpolar\MemoryUsage)
- [x] Memory usage shall be below 800000 bytes

## Project Size (Phpolar\Phpolar\ProjectSize)
- [x] Source code total size shall be below 30000 bytes

## Routing (Phpolar\Phpolar\Routing\Routing)
- [x] Shall invoke the handler registered to the given route
- [x] Shall return a "not found" response when the given route has not been registered

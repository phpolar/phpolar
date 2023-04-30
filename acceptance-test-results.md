# Acceptance Test Report

## Automatic Html Encoding (Phpolar\Phpolar\AutomaticHtmlEncoding)
- [x] Should prevent cross-site scripting injection

## Configurable Form Field (Phpolar\Phpolar\ConfigurableFormField)
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
- [x] Memory usage shall be below 650000 bytes

## Project Size (Phpolar\Phpolar\ProjectSize)
- [x] Source code total size shall be below 25500 bytes

## Routing (Phpolar\Phpolar\Routing\Routing)
- [x] Shall invoke the handler registered to the given route
- [x] Shall return a "not found" response when the given route has not been registered

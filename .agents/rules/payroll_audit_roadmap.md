# Payroll Architecture Audit & Market-Standard Roadmap

This project contains a preliminary payroll module that has been audited against commercial market standards (Keka HR, Zoho Payroll, GreytHR).

## Persistent Memory Summary
- **Current State**: Basic salary types (Flat/Percentage), attendance-linked LOP deduction, single-run bulk generation, PDF payslip export.
- **Identified Critical Bugs**:
  - Missing `$halfday_deduction` in total deduction calculation (`SalarytypeController.php:362`).
  - Mid-month joiners/exiters falsely penalized for working days prior to joining date.
  - Hardcoded `/ 30` day divisor breaking February and 31-day months.
  - Code duplicated across 3 controllers (`SalarytypeController`, `EmployeePayrollManagementController`, `Api\SalaryController`).
- **Missing Market Standards**:
  - True CTC breakdown (Annual CTC, Monthly Gross, Special Allowance balancing figure).
  - Statutory compliance engine: EPF (with ECR export), ESIC, State Professional Tax (PT), TDS / Tax declarations.
  - Payroll lifecycle and locks (Draft -> Review -> Approve -> Disburse -> Lock).
  - Payout integration (Bank NEFT/RTGS files, RazorpayX API).
  - Full & Final settlement (Gratuity, leave encashment).

When the user says "go ahead" or asks to proceed with payroll improvements, refer to the complete specifications in:
- Artifact: `payroll_audit_and_market_standard_roadmap.md`
- Begin with **Phase 1: Engine Consolidation & Bug Fixes** (`App\Services\Payroll\PayrollCalculatorService`).

# ecard-prestashop1.7
Repo pour prestashop 1.7

## Changelog
### 2.1.5 to 2.2.0
- Change all static calls to `Logger` to `PrestaShopLogger` to help IDEs
- PledgParams: concatenate `Address` and `Address complement` to send to Pledg API
- PledgParams: add posibility to use different merchantIds according to customer's country
- Metadata: add `account` information (`creation_date` and `number_of_purchases`)
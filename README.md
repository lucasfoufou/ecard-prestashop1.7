# ecard-prestashop1.7
Repo pour prestashop 1.7

## Changelog
### 2.2.5
- Extend PaymentModule instead of PaymentModuleCore
  
### 2.2.4
- Only convert amount received in notification when the currency is different from the cart
  
### 2.2.3
- Prevent the plugin to block a notification when notified amount is different from order amount for less than 1% (to prevent a round or conversion false negative)
- Don't send birthday when it is null
  
### 2.2.2
- Only load Js and Css when the controller is "AdminPledg"
- Add a security to prevent the plugin from reverting a notification during the validation (in a case where the validation happens after the notification)
  
### 2.2.1
- Fix Notice Undefined property: Cart::getCurrency
  
### 2.1.5 to 2.2.0
- Change all static calls to `Logger` to `PrestaShopLogger` to help IDEs
- PledgParams: concatenate `Address` and `Address complement` to send to Pledg API
- PledgParams: add posibility to use different merchantIds according to customer's country
- Metadata: add `account` information (`creation_date` and `number_of_purchases`)
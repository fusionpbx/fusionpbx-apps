******
TO DO
******
* Security: Get provisioning IP Addresses from Sessiontalk. They send all provision requests from their cloud platform instead of directly from the phones. Limit the PHP code to only accept requests from those IP addresses, stored in a default setting for easy updates.
* Enhancement: Store App Store Links in Default Settings to support White Label Apps.
* Enhancement: Prevent QR Code generation if the max_activations is already met for a device.
* Enhancement: Add Email QR Code button and app_config or app_defaults code to inject a template for the QR Code into the Email Templates table.
* Enhancement: Add language support for API error messages. We can get the language from the domain in the username.
* Enhancement: Add button "Invalidate All Active QR Codes" to clear the stored keys
* Enhancement: Support One-Off setup with a QR Code instead of requiring the provision API. The QR contains the same JSON as the API response would include including SIP Credentials.
* Enhancement: Update Translations to have accurate text. English has accurate instructions now, other languages not so much.
* Enhancement: Re-implement User API Key QR Code method from the previous version of this app. Either that or provide instructions to download that version of this app.
* Any changes to provision.php should validate this list of scenarios
   * Invalid QR Empty Device ID
   * Valid QR Empty Device ID
   * Empty QR Existing Device ID
   * Empty QR New Device ID
   * Empty QR Empty Device ID
   * Expired QR Empty DeviceID
   * New Device Expired QR
   * Old Device Same Expired QR
   * Old Device Different Expired QR
   * New Device New Valid QR
   * New Device Invalid QR (non-decryptable)
   * Old Device New QR
   * Old Device Same Valid QR
   * Old Device Different Used Valid QR
   * Reprovision All Lines Deleted/disabled
   * Reprovision Device disabled
   * Reprovision Device Deleted
   * Reprovision Valid QR New DeviceID
   * New Device Used Valid QR
* Lament the fact that half of my concerns about security will be assuaged simply by limiting the API to Sessiontalk's provisioning IP addresses. If they update it in the future to come directly from the app all this security might be nice to have.




Sessiontalk API Documentation: https://www.sessiontalk.co.uk/help-articles/using-qr-codes-with-your-provisioning-server

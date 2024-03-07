<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'MainPage';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*USER User/processEditUser*/ 
$route['user']                                        = 'User';
$route['change-password']                             = 'User/changePassword';
$route['change-password/(:num)']                      = 'User/changePassword/$1';
$route['user/add']                                    = 'User/Add';
$route['user/edit']                                   = 'User/Edit';
$route['user/edit/(:num)']                            = 'User/Edit/$1';
$route['user/delete/(:num)']                          = 'User/delete/$1';
$route['user/default-password/(:num)']                = 'User/defaultpassword/$1';
$route['user/process-add']                            = 'User/processAddUser';
$route['user/process-edit']                           = 'User/processEditUser';


/* CORE MEMBER */
$route['member']                                        = 'CoreMember';
$route['member/get-member']                             = 'CoreMember/getMasterDataCoreMember';
$route['member/check-name-placeofbirth']                = 'CoreMember/getMemberNameAndPlaceOfBirth';
$route['member/get-member-list']                        = 'CoreMember/getCoreMemberList';
$route['member/get-list-status']                        = 'CoreMember/getUpdateCoreMemberStatusList';
$route['member/get-list-edit']                          = 'CoreMember/getListCoreMemberEdit';
$route['member/get-list-edit-debet']                    = 'CoreMember/getListCoreMemberEditDebet';
$route['member/elements-edit']                          = 'CoreMember/function_elements_edit';
$route['member/elements-add']                           = 'CoreMember/function_elements_add';
$route['member/filter-mutation']                        = 'CoreMember/filterMutation';
$route['member/add']                                    = 'CoreMember/addCoreMember';
$route['member/edit/(:num)']                            = 'CoreMember/editCoreMember/$1';
$route['member/detail/(:num)']                          = 'CoreMember/showdetail/$1';
$route['member/delete/(:num)']                          = 'CoreMember/deleteCoreMember/$1';
$route['member/activate/(:num)']                        = 'CoreMember/activateCoreMember/$1';
$route['member/non-activate/(:num)']                    = 'CoreMember/nonActivateCoreMember/$1';
$route['member/edit-member-savings/(:num)']             = 'CoreMember/editCoreMemberSavings/$1';
$route['member/edit-member-savings']                    = 'CoreMember/editCoreMemberSavings';
$route['member/edit-debet-member-savings']              = 'CoreMember/editDebetCoreMemberSavings';
$route['member/edit-debet-member-savings/(:num)']       = 'CoreMember/editDebetCoreMemberSavings/$1';
$route['member/edit-debet-member-savings/(:num)/(:num)']= 'CoreMember/editDebetCoreMemberSavings/$1/$1';
$route['member/process-edit-member-savings']            = 'CoreMember/processEditCoreMemberSavings';
$route['member/process-edit-debet-member-savings']      = 'CoreMember/processEditDebetCoreMemberSavings';
$route['member/process-edit-member']                    = 'CoreMember/processEditCoreMember';
$route['member/process-edit-utility']                   = 'CoreMember/processAddCoreMemberUtility';
$route['member/process-add']                            = 'CoreMember/processAddCoreMember';
$route['member/process-printing/(:num)']                = 'CoreMember/processPrinting/$1';
$route['member/process-update-status/(:num)']           = 'CoreMember/processUpdateCoreMemberStatus/$1';
$route['member/update-status']                          = 'CoreMember/updateCoreMemberStatus';
$route['member/print-mutation']                         = 'CoreMember/printMutationCoreMember';
$route['member/print-mutation/(:num)']                  = 'CoreMember/printMutationCoreMember/$1';
$route['member/add-utility']                            = 'CoreMember/addCoreMemberUtility';
$route['member/print-book']                             = 'CoreMember/printBookCoreMember';
$route['member/process-print-book/(:num)']              = 'CoreMember/processPrintCoverBookCoreMember/$1';
$route['member/filter']                                 = 'CoreMember/filter';
$route['member/filter-master']                          = 'CoreMember/filterMasterData';
$route['member/export-master-data']                     = 'CoreMember/exportMasterDataCoreMember';
$route['member/reset-search-mutation']                  = 'CoreMember/reset_search_mutation';
$route['member/reset-edit-member/(:num)']               = 'CoreMember/reset_edit_member/$1';
$route['member/reset-edit/(:num)']                      = 'CoreMember/reset_edit/$1';
$route['member/reset-add']                              = 'CoreMember/reset_add';
$route['member/process-print-mutasi/preview']           = 'CoreMember/processPrintMutasiCoreMember/$1';
$route['member/process-print-mutasi/print']             = 'CoreMember/processPrintMutasiCoreMember/$1';
$route['member/process-print-mutasi/preview/(:num)']    = 'CoreMember/processPrintMutasiCoreMember/$1/$1';
$route['member/process-print-mutasi/print/(:num)']      = 'CoreMember/processPrintMutasiCoreMember/$1/$1';
$route['member/filter-mutation']                        = 'CoreMember/filterMutation';
$route['member/get-city/(:num)']                        = 'CoreMember/getCoreCity/$1';
$route['member/get-city']                               = 'CoreMember/getCoreCity';
$route['member/get-kecamatan/(:num)']                   = 'CoreMember/getCoreKecamatan/$1';
$route['member/get-kecamatan']                          = 'CoreMember/getCoreKecamatan';
$route['member/get-kelurahan/(:num)']                   = 'CoreMember/getCoreKelurahan/$1';
$route['member/get-dusun/(:num)']                       = 'CoreMember/getCoreDusun//$1';
$route['member/get-mutation-function']                  = 'CoreMember/getMutationFunction';
$route['member/get-list-mutation']                      = 'CoreMember/getListCoreMemberMutation';
$route['member/get-list-mutation']                      = 'CoreMember/getListCoreMemberMutation';
$route['member/change-member-class/(:num)']             = 'CoreMember/changeMemberClass/$1';
$route['member/change-company/(:num)']                  = 'CoreMember/changeCompany/$1';


// CORE MEMBER TRANSFER MUTATION
$route['member-transfer-mutation']                                 = 'CoreMemberTransferMutation';
$route['member-transfer-mutation/add']                             = 'CoreMemberTransferMutation/addCoreMemberTransferMutation';
$route['member-transfer-mutation/add-member']                      = 'CoreMemberTransferMutation/addCoreMember';
$route['member-transfer-mutation/process-add']                     = 'CoreMemberTransferMutation/processAddCoreMemberTransferMutation';
$route['member-transfer-mutation/reset']                           = 'CoreMemberTransferMutation/reset_data';
$route['member-transfer-mutation/filter']                          = 'CoreMemberTransferMutation/filter';
$route['member-transfer-mutation/add/(:num)']                      = 'CoreMemberTransferMutation/addCoreMemberTransferMutation/$1';
$route['member-transfer-mutation/add/(:num)/(:num)']               = 'CoreMemberTransferMutation/addCoreMemberTransferMutation/$1/$1';
$route['member-transfer-mutation/print-validation/(:num)']         = 'CoreMemberTransferMutation/printValidationCoreMemberTransferMutation/$1';
$route['member-transfer-mutation/validation/(:num)']               = 'CoreMemberTransferMutation/validationCoreMemberTransferMutation/$1';
$route['member-transfer-mutation/print-mutation/(:num)']           = 'CoreMemberTransferMutation/printCoreMemberTransferMutation/$1';
$route['member-transfer-mutation/add-savings-account/(:num)']      = 'CoreMemberTransferMutation/addAcctSavingsAccount/$1';
$route['member-transfer-mutation/add-savings-account']             = 'CoreMemberTransferMutation/addAcctSavingsAccount';

// ACCT SAVINGS TRANSFER MUTATION 
$route['savings-transfer-mutation']                                 = 'AcctSavingsTransferMutation';
$route['savings-transfer-mutation/reset-data']                      = 'AcctSavingsTransferMutation/reset_data';
$route['savings-transfer-mutation/reset-search']                    = 'AcctSavingsTransferMutation/reset_search';
$route['savings-transfer-mutation/get-list-from']                   = 'AcctSavingsTransferMutation/getListAcctSavingsAccountFrom';
$route['savings-transfer-mutation/get-list-to']                     = 'AcctSavingsTransferMutation/getListAcctSavingsAccountTo';
$route['savings-transfer-mutation/filter']                          = 'AcctSavingsTransferMutation/filter';
$route['savings-transfer-mutation/filter-from']                     = 'AcctSavingsTransferMutation/filterListAcctSavingsAccountFrom';
$route['savings-transfer-mutation/filter-to']                       = 'AcctSavingsTransferMutation/filterListAcctSavingsAccountTo';
$route['savings-transfer-mutation/add-savings-account-to/(:num)']   = 'AcctSavingsTransferMutation/addAcctSavingsAccountTo/$1';
$route['savings-transfer-mutation/add-savings-account-to']          = 'AcctSavingsTransferMutation/addAcctSavingsAccountTo';
$route['savings-transfer-mutation/print-validation/(:num)']         = 'AcctSavingsTransferMutation/printValidationAcctSavingsTransferMutation/$1';
$route['savings-transfer-mutation/validation/(:num)']               = 'AcctSavingsTransferMutation/ValidationAcctSavingsTransferMutation/$1';
$route['savings-transfer-mutation/add/(:num)']                      = 'AcctSavingsTransferMutation/addAcctSavingsTransferMutation/$1';
$route['savings-transfer-mutation/add-savings-account-from']        = 'AcctSavingsTransferMutation/addAcctSavingsAccountFrom';
$route['savings-transfer-mutation/process-add']                     = 'AcctSavingsTransferMutation/processAddAcctSavingsTransferMutation';
$route['savings-transfer-mutation/add/(:num)/(:num)']               = 'AcctSavingsTransferMutation/addAcctSavingsTransferMutation/$1/$1';
$route['savings-transfer-mutation/add/(:num)']                      = 'AcctSavingsTransferMutation/addAcctSavingsTransferMutation/$1';
$route['savings-transfer-mutation/add']                             = 'AcctSavingsTransferMutation/addAcctSavingsTransferMutation';

//ACCT SAVINGS CASH MUTATION BRANCH
$route['savings-cash-mutation-branch']                                      = 'AcctSavingsCashMutationBranch';
$route['savings-cash-mutation-branch/elements-add']                         = 'AcctSavingsCashMutationBranch/function_elements_add';
$route['savings-cash-mutation-branch/reset-data']                           = 'AcctSavingsCashMutationBranch/reset_data';
$route['savings-cash-mutation-branch/reset-search']                         = 'AcctSavingsCashMutationBranch/reset_search';
$route['savings-cash-mutation-branch/get-mutation']                         = 'AcctSavingsCashMutationBranch/getMutationFunction';
$route['savings-cash-mutation-branch/add']                                  = 'AcctSavingsCashMutationBranch/addAcctSavingsCashMutationBranch';
$route['savings-cash-mutation-branch/process-add']                          = 'AcctSavingsCashMutationBranch/processAddAcctSavingsCashMutationBranch';
$route['savings-cash-mutation-branch/get-list']                             = 'AcctSavingsCashMutationBranch/getListAcctSavingsAccount';
$route['savings-cash-mutation-branch/get-savings-cash-mutation-branch']     = 'AcctSavingsCashMutationBranch/getAcctSavingsCashMutationBranch';
$route['savings-cash-mutation-branch/filter']                               = 'AcctSavingsCashMutationBranch/filter';
$route['savings-cash-mutation-branch/add/(:num)']                           = 'AcctSavingsCashMutationBranch/addAcctSavingsCashMutationBranch/$1';
$route['savings-cash-mutation-branch/print-note/(:num)']                    = 'AcctSavingsCashMutationBranch/printNoteAcctSavingsCashMutationBranch/$1';
$route['savings-cash-mutation-branch/print-validation/(:num)']              = 'AcctSavingsCashMutationBranch/printValidationAcctSavingsCashMutationBranch/$1';
$route['savings-cash-mutation-branch/validation/(:num)']                    = 'AcctSavingsCashMutationBranch/validationAcctSavingsCashMutationBranch/$1';

//ACCT SAVINGS BANK MUTATION
$route['savings-bank-mutation']                             = 'AcctSavingsBankMutation';
$route['savings-bank-mutation/add']                         = 'AcctSavingsBankMutation/addAcctSavingsBankMutation';
$route['savings-bank-mutation/elements-add']                = 'AcctSavingsBankMutation/function_elements_add';
$route['savings-bank-mutation/reset-data']                  = 'AcctSavingsBankMutation/reset_data';
$route['savings-bank-mutation/reset-search']                = 'AcctSavingsBankMutation/reset_search';
$route['savings-bank-mutation/get-list-savings-account']    = 'AcctSavingsBankMutation/getListAcctSavingsAccount';
$route['savings-bank-mutation/get-savings-account-detail']  = 'AcctSavingsBankMutation/getAcctSavingsAccount_Detail';
$route['savings-bank-mutation/get-mutation']                = 'AcctSavingsBankMutation/getMutationFunction';
$route['savings-bank-mutation/process-add']                 = 'AcctSavingsBankMutation/processAddAcctSavingsBankMutation';
$route['savings-bank-mutation/process-void']                = 'AcctSavingsBankMutation/processVoidAcctSavingsBankMutation';
$route['savings-bank-mutation/add/(:num)']                  = 'AcctSavingsBankMutation/addAcctSavingsBankMutation/$1';
$route['savings-bank-mutation/filter']                      = 'AcctSavingsBankMutation/filter';

//ACCT DEPOSITO PROFIT SHARING CHECK
$route['deposito-profit-sharing-check']                                    = 'AcctDepositoProfitSharingCheck';
$route['deposito-profit-sharing-check/add/(:num)']                         = 'AcctDepositoProfitSharingCheck/addAcctDepositoProfitSharingCheck/$1';
$route['deposito-profit-sharing-check/add/(:num)/(:num)']                  = 'AcctDepositoProfitSharingCheck/addAcctDepositoProfitSharingCheck/$1/$1';
$route['deposito-profit-sharing-check/add']                                = 'AcctDepositoProfitSharingCheck/addAcctDepositoProfitSharingCheck';
$route['deposito-profit-sharing-check/process-update']                     = 'AcctDepositoProfitSharingCheck/processUpdateAcctDepositoProfitSharing';
$route['deposito-profit-sharing-check/get-savings-account-list/(:num)']    = 'AcctDepositoProfitSharingCheck/getAcctSavingsAccountList/$1';
$route['deposito-profit-sharing-check/filter']                             = 'AcctDepositoProfitSharingCheck/filter';
$route['deposito-profit-sharing-check/reset-search']                       = 'AcctDepositoProfitSharingCheck/reset_search';

//CORE MEMBER MBAYAR
$route['member-mbayar']                                 = 'CoreMemberMbayar';
$route['member-mbayar/add']                             = 'CoreMemberMbayar/addCoreMemberMbayar';
$route['member-mbayar/filter']                          = 'CoreMemberMbayar/filter';
$route['member-mbayar/reset-add']                       = 'CoreMemberMbayar/reset_add';
$route['member-mbayar/process-add']                     = 'CoreMemberMbayar/processAddCoreMemberMbayar';
$route['member-mbayar/get-list-member-edit']            = 'CoreMemberMbayar/getListCoreMemberEdit';
$route['member-mbayar/get-list-member-mbayar']          = 'CoreMemberMbayar/getCoreMemberMbayarList';
$route['member-mbayar/get-list-savings-account/(:num)'] = 'CoreMemberMbayar/getListAcctSavingsAccount/$1';
$route['member-mbayar/add/(:num)']                      = 'CoreMemberMbayar/addCoreMemberMbayar/$1';
$route['member-mbayar/process-print-qrcode/(:num)']     = 'CoreMemberMbayar/processPrintingQRCode/$1';


//ACCT SAVINGS ACCOUNT
$route['savings-account']                           = 'AcctSavingsAccount';
$route['savings-account/filter']                    = 'AcctSavingsAccount/filter';
$route['savings-account/filter-list-member']        = 'AcctSavingsAccount/filterListCoreMember';
$route['savings-account/filter-master']             = 'AcctSavingsAccount/filtermasterdata';
$route['savings-account/get-savings-account-no']    = 'AcctSavingsAccount/getSavingsAccountNo';
$route['savings-account/elements-add']              = 'AcctSavingsAccount/function_elements_add';
$route['savings-account/reset-list']                = 'AcctSavingsAccount/reset_list';
$route['savings-account/reset-search']              = 'AcctSavingsAccount/reset_search';
$route['savings-account/add']                       = 'AcctSavingsAccount/addAcctSavingsAccount';
$route['savings-account/add/(:num)']                = 'AcctSavingsAccount/addAcctSavingsAccount/$1';
$route['savings-account/process-add']               = 'AcctSavingsAccount/processAddAcctSavingsAccount';
$route['savings-account/process-void']              = 'AcctSavingsAccount/processVoidAcctSavingsAccount';
$route['savings-account/get-list']                  = 'AcctSavingsAccount/getAcctSavingsAccountList';
$route['savings-account/print-note/(:num)']         = 'AcctSavingsAccount/printNoteAcctSavingsAccount/$1';
$route['savings-account/print-validation/(:num)']   = 'AcctSavingsAccount/printValidationAcctSavingsAccount/$1';
$route['savings-account/validation/(:num)']         = 'AcctSavingsAccount/validationAcctSavingsAccount/$1';
$route['savings-account/get-master']                = 'AcctSavingsAccount/getMasterDataSavingsAccount';
$route['savings-account/get-master-list']           = 'AcctSavingsAccount/getMasterDataSavingsAccountList';
$route['savings-account/get-list-member']           = 'AcctSavingsAccount/getListCoreMember';
$route['savings-account/get-kecamatan']             = 'AcctSavingsAccount/getCoreKecamatan';
$route['savings-account/export-master-data']        = 'AcctSavingsAccount/exportMasterDataAcctSavingsAccount';

//ACCT CREDIT ACCOUNT
$route['credit-account']                                           = 'AcctCreditAccount';
$route['credit-account/add-form']                                  = 'AcctCreditAccount/addform';
$route['credit-account/book']                                      = 'AcctCreditAccount/AcctCreditAccountBook';
$route['credit-account/reset']                                     = 'AcctCreditAccount/reset';
$route['credit-account/reset-data']                                = 'AcctCreditAccount/reset_data';
$route['credit-account/reset-search']                              = 'AcctCreditAccount/reset_search';
$route['credit-account/get-credits-account-detail-list']           = 'AcctCreditAccount/getAcctCreditsAccountDetailList';
$route['credit-account/detail']                                    = 'AcctCreditAccount/detailAcctCreditsAccount';
$route['credit-account/get-credits-account-list']                  = 'AcctCreditAccount/getAcctCreditsAccountList';
$route['credit-account/get-credits-account-book-list']             = 'AcctCreditAccount/getAcctCreditsAccountBookList';
$route['credit-account/cek-pola-angsuran']                         = 'AcctCreditAccount/cekPolaANgsuran';
$route['credit-account/pola-angsuran']                             = 'AcctCreditAccount/polaangsuran';
$route['credit-account/process-printing']                          = 'AcctCreditAccount/processPrinting';
$route['credit-account/add-agunan']                                = 'AcctCreditAccount/agunanadd';
$route['credit-account/process-add-array-agunan']                  = 'AcctCreditAccount/processAddArrayAgunan';
$route['credit-account/member-list']                               = 'AcctCreditAccount/memberlist';
$route['credit-account/print-pola-angsuran']                       = 'AcctCreditAccount/printPolaAngsuran';
$route['credit-account/add-form/(:num)']                           = 'AcctCreditAccount/addform/$1';
$route['credit-account/show-detail-data/(:num)/(:num)']            = 'AcctCreditAccount/showdetaildata/$1/$1';
$route['credit-account/show-detail/(:num)']                        = 'AcctCreditAccount/showdetail/$1';
$route['credit-account/angsuran/(:num)/(:num)']                    = 'AcctCreditAccount/angsuran/$1/$1';
$route['credit-account/angsuran/(:num)']                           = 'AcctCreditAccount/angsuran/$1';
$route['credit-account/approving/(:num)']                          = 'AcctCreditAccount/Approving/$1';
$route['credit-account/process-approve']                           = 'AcctCreditAccount/processApprove';
$route['credit-account/reject/(:num)']                             = 'AcctCreditAccount/rejectAcctCreditsAccount/$1';
$route['credit-account/print-note/(:num)']                         = 'AcctCreditAccount/printNoteAcctCreditAccount/$1';
$route['credit-account/print-book/(:num)']                         = 'AcctCreditAccount/printBookAcctCreditAccount/$1';
$route['credit-account/process-print-akad/(:num)']                 = 'AcctCreditAccount/processPrintingAkad/$1';
$route['credit-account/print-schedule-credits-payment/(:num)']     = 'AcctCreditAccount/printScheduleCreditsPayment/$1';
$route['credit-account/print-schedule-credits-payment-member/(:num)']     = 'AcctCreditAccount/printScheduleCreditsPaymentMember/$1';
$route['credit-account/print-agunan-receipt/(:num)']                     = 'AcctCreditAccount/printAgunanReceipt/$1';
$route['credit-account/print-pola-angsuran-credits/(:num)']        = 'AcctCreditAccount/printPolaAngsuranCredits/$1';
$route['credit-account/add-function-element']                      = 'AcctCreditAccount/function_elements_add';
$route['credit-account/get-credits-account-serial']                = 'AcctCreditAccount/getCreditsAccountSerial';
$route['credit-account/add']                                       = 'AcctCreditAccount/addcreditaccount';
$route['credit-account/credit-ajax']                               = 'AcctCreditAccount/creditajax';
$route['credit-account/filter']                                    = 'AcctCreditAccount/filter';
$route['credit-account/filter-credits-account']                    = 'AcctCreditAccount/filteracctcreditsaccount';
$route['credit-account/filter-credits-account-book']               = 'AcctCreditAccount/filteracctcreditsaccountbook';
$route['credit-account/filter-detail']                             = 'AcctCreditAccount/filterdetail';
$route['credit-account/agunan-add/save']                           = 'AcctCreditAccount/agunanadd';
$route['credit-account/edit-date']                                 = 'AcctCreditAccount/editDateAcctCreditAccount';
$route['credit-account/edit-date/(:num)']                          = 'AcctCreditAccount/editDateAcctCreditAccount/$1';
$route['credit-account/process-edit-date']                         = 'AcctCreditAccount/processEditDateAcctCreditAccount';
$route['credit-account/delete/(:num)']                             = 'AcctCreditAccount/deleteAcctCreditAccount/$1';
$route['credit-account/rate4']                                     = 'AcctCreditAccount/rate4';


//ACCT DEPOSITO ACCOUNT 
$route['deposito-account']                                  = 'AcctDepositoAccount';
$route['deposito-account/get-master-data-list']             = 'AcctDepositoAccount/getMasterDataAcctDepositoAccountList';
$route['deposito-account/get-list-core-member']             = 'AcctDepositoAccount/getListCoreMember';
$route['deposito-account/get-list-savings-account']         = 'AcctDepositoAccount/getListAcctSavingAccount';
$route['deposito-account/get-list-savings-account/(:num)']  = 'AcctDepositoAccount/getListAcctSavingAccount/$1';
$route['deposito-account/export']                           = 'AcctDepositoAccount/exportMasterDataAcctDepositoAccount';
$route['deposito-account/add']                              = 'AcctDepositoAccount/addAcctDepositoAccount';
$route['deposito-account/filter']                           = 'AcctDepositoAccount/filter';
$route['deposito-account/filter-closed-deposito-account']   = 'AcctDepositoAccount/filterClosedAcctDepositoAccount';
$route['deposito-account/filter-deposito-count-due-date']   = 'AcctDepositoAccount/filterAcctDepositoAccountDueDate';
$route['deposito-account/filter-list-deposito-account']     = 'AcctDepositoAccount/filterListAcctDepositoAccount';
$route['deposito-account/filter-list-savings-account']      = 'AcctDepositoAccount/filterListAcctSavingsAccount';
$route['deposito-account/filter-member']                    = 'AcctDepositoAccount/filterListCoreMember';
$route['deposito-account/filter-master-data']               = 'AcctDepositoAccount/filtermasterdata';
$route['deposito-account/elements-add']                     = 'AcctDepositoAccount/add-function-element';
$route['deposito-account/add-new-deposito-account/(:num)']  = 'AcctDepositoAccount/addNewAcctDepositoAccount/$1';
$route['deposito-account/add-new-deposito-account']         = 'AcctDepositoAccount/addNewAcctDepositoAccount';
$route['deposito-account/get-deposite-account-no']          = 'AcctDepositoAccount/getDepositoAccountNo';
$route['deposito-account/get-savings-account-list/(:num)']  = 'AcctDepositoAccount/getAcctSavingsAccountList/$1';
$route['deposito-account/reset']                            = 'AcctDepositoAccount/reset_data';
$route['deposito-account/reset-close/(:num)']               = 'AcctDepositoAccount/reset_close/$1';
$route['deposito-account/reset-search']                     = 'AcctDepositoAccount/reset_search';
$route['deposito-account/reset-search-duedate']             = 'AcctDepositoAccount/reset_search_duedate';
$route['deposito-account/reset-search-closed']              = 'AcctDepositoAccount/reset_search_closed';
$route['deposito-account/add-function-element']             = 'AcctDepositoAccount/function_elements_add';
$route['deposito-account/get-list']                         = 'AcctDepositoAccount/getListAcctDepositoAccount';
$route['deposito-account/get-master']                       = 'AcctDepositoAccount/getMasterDataAcctDepositoAccount';
$route['deposito-account/get-due-date']                     = 'AcctDepositoAccount/getAcctDepositoAccountDueDate';
$route['deposito-account/deposito-account-due-date']        = 'AcctDepositoAccount/AcctDepositoAccountDueDate';
$route['deposito-account/deposito-account/(:num)']          = 'AcctDepositoAccount/AcctDepositoAccount/$1';
$route['deposito-account/get-closed']                       = 'AcctDepositoAccount/getClosedAcctDepositoAccount';
$route['deposito-account/get-list-print']                   = 'AcctDepositoAccount/getListPrintAcctDepositoAccount';
$route['deposito-account/get-core-member-detail']           = 'AcctDepositoAccount/getCoreMember_Detail';
$route['deposito-account/get-kecamatan']                    = 'AcctDepositoAccount/getCoreKecamatan';
$route['deposito-account/list']                             = 'AcctDepositoAccount/listAcctDepositoAccount';
$route['deposito-account/closed']                           = 'AcctDepositoAccount/ClosedAcctDepositoAccount';
$route['deposito-account/process-closed']                   = 'AcctDepositoAccount/processClosedAcctDepositoAccount';
$route['deposito-account/add-closed/(:num)']                = 'AcctDepositoAccount/addClosedAcctDepositoAccount/$1';
$route['deposito-account/add-closed/(:num)/(:num)']         = 'AcctDepositoAccount/addClosedAcctDepositoAccount/$1/$1';
$route['deposito-account/add-extra/(:num)']                 = 'AcctDepositoAccount/addAcctDepositoAccountExtra/$1';
$route['deposito-account/process-add-extra']                = 'AcctDepositoAccount/processAddAcctDepositoAccountExtra';
$route['deposito-account/process-add']                      = 'AcctDepositoAccount/processAddAcctDepositoAccount';
$route['deposito-account/process-void']                     = 'AcctDepositoAccount/processVoidAcctDepositoAccount';
$route['deposito-account/process-add-new']                  = 'AcctDepositoAccount/processAddNewAcctDepositoAccount';
$route['deposito-account/add/(:num)']                       = 'AcctDepositoAccount/addAcctDepositoAccount/$1';
$route['deposito-account/add/(:num)/(:num)']                = 'AcctDepositoAccount/addAcctDepositoAccount/$1/$1';
$route['deposito-account/edit/(:num)']                      = 'AcctDepositoAccount/editAcctDepositoAccount/$1';
$route['deposito-account/delete/(:num)']                    = 'AcctDepositoAccount/deleteAcctDeposito/$1';
$route['deposito-account/print-note/(:num)']                = 'AcctDepositoAccount/printNoteAcctDepositoAccount/$1';
$route['deposito-account/print-bilyet/(:num)']              = 'AcctDepositoAccount/printBilyetAcctDepositoAccount/$1';
$route['deposito-account/print-validation/(:num)']          = 'AcctDepositoAccount/printValidationAcctDepositoAccount/$1';
$route['deposito-account/validation/(:num)']                = 'AcctDepositoAccount/ValidationAcctDepositoAccount/$1';
$route['deposito-account/print-validation-closed/(:num)']   = 'AcctDepositoAccount/printValidationClosedAcctDepositoAccount/$1';
$route['deposito-account/print-certificate-front/(:num)']   = 'AcctDepositoAccount/printCertificateAcctDepositoAccountFront/$1';
$route['deposito-account/print-certificate-back/(:num)']    = 'AcctDepositoAccount/printCertificateAcctDepositoAccountBack/$1';
$route['deposito-account/print-certificate-deposito']       = 'AcctDepositoAccount/printCertificateDeposito';
$route['deposito-account/delete/(:num)']                    = 'AcctDepositoAccount/deleteAcctDeposito/$1';

//ACCT SAVINGS PRINT MUTATION
$route['savings-print-mutation/get-savings-mutation']                   = 'AcctSavingsPrintMutation/getAcctSavingsAccount';
$route['savings-print-mutation']                                        = 'AcctSavingsPrintMutation';
$route['savings-print-mutation/reset-book']                             = 'AcctSavingsPrintMutation/reset_search_book';
$route['savings-print-mutation/reset-search']                           = 'AcctSavingsPrintMutation/reset_search';
$route['savings-print-mutation/filter-book']                            = 'AcctSavingsPrintMutation/filterbook';
$route['savings-print-mutation/filter']                                 = 'AcctSavingsPrintMutation/filter';
$route['savings-print-mutation/get-list-savings-account-book']          = 'AcctSavingsPrintMutation/getListAcctSavingsAccountBook';
$route['savings-print-mutation/get-list-savings-account']               = 'AcctSavingsPrintMutation/getListAcctSavingsAccount';
$route['savings-print-mutation/monitor-savings-mutation']               = 'AcctSavingsPrintMutation/MonitorSavingsMutation';
$route['savings-print-mutation/elements-add']                           = 'AcctSavingsPrintMutation/function_elements_add';
$route['savings-print-mutation/monitor-savings-mutation/(:num)']        = 'AcctSavingsPrintMutation/MonitorSavingsMutation/$1';
$route['savings-print-mutation/process-print-cover-book/(:num)']        = 'AcctSavingsPrintMutation/processPrintCoverBook/$1';
$route['savings-print-mutation/process-printing/preview/(:num)/(:num)'] = 'AcctSavingsPrintMutation/processPrinting/$1/$1';
$route['savings-print-mutation/process-printing/print/(:num)/(:num)']   = 'AcctSavingsPrintMutation/processPrinting/$1/$1';
$route['savings-print-mutation/process-printing/preview/(:num)']        = 'AcctSavingsPrintMutation/processPrinting/$1';
$route['savings-print-mutation/process-printing/print/(:num)']          = 'AcctSavingsPrintMutation/processPrinting/$1';
$route['savings-print-mutation/process-printing/(:num)']                = 'AcctSavingsPrintMutation/processPrinting/$1';


//ACCT SAVINGS PRINT SAVINGS MONITOR  
$route['savings-print-savings-monitor/monitor-savings-mutation']         = 'AcctSavingsPrintSavingsMonitor/MonitorSavingsMutation';
$route['savings-print-savings-monitor/get-list-savings-account']         = 'AcctSavingsPrintSavingsMonitor/getListAcctSavingsAccount';
$route['savings-print-savings-monitor/filter-savings-account']           = 'AcctSavingsPrintSavingsMonitor/filterAcctSavingsAccount';
$route['savings-print-savings-monitor/filter']                           = 'AcctSavingsPrintSavingsMonitor/filter';
$route['savings-print-savings-monitor/get-list-savings-monitor/(:num)']  = 'AcctSavingsPrintSavingsMonitor/getListAcctSavingsMonitor/$1';
$route['savings-print-savings-monitor/reset-search']                     = 'AcctSavingsPrintSavingsMonitor/reset_search';
$route['savings-print-savings-monitor/syncronize-data']                  = 'AcctSavingsPrintSavingsMonitor/SyncronizeData';
$route['savings-print-savings-monitor/syncronize-data/(:num)']           = 'AcctSavingsPrintSavingsMonitor/SyncronizeData/$1';
$route['savings-print-savings-monitor/process-print']                    = 'AcctSavingsPrintSavingsMonitor/processPrinting';
$route['savings-print-savings-monitor']                                  = 'AcctSavingsPrintSavingsMonitor';
$route['savings-print-savings-monitor/monitor-savings-mutation/(:num)']  = 'AcctSavingsPrintSavingsMonitor/MonitorSavingsMutation/$1';


//ACCT SAVINGS CASH MUTATION
$route['savings-cash-mutation']                             = 'AcctSavingsCashMutation';
$route['savings-cash-mutation/reset-search']                = 'AcctSavingsCashMutation/reset_search';
$route['savings-cash-mutation/add']                         = 'AcctSavingsCashMutation/addAcctSavingsCashMutation';
$route['savings-cash-mutation/reset']                       = 'AcctSavingsCashMutation/reset_data';
$route['savings-cash-mutation/elements-add']                = 'AcctSavingsCashMutation/function_elements_add';
$route['savings-cash-mutation/add/(:num)']                  = 'AcctSavingsCashMutation/addAcctSavingsCashMutation/$1';
$route['savings-cash-mutation/void/(:num)']                 = 'AcctSavingsCashMutation/voidAcctSavingsCashMutation/$1';
$route['savings-cash-mutation/void']                        = 'AcctSavingsCashMutation/voidAcctSavingsCashMutation';
$route['savings-cash-mutation/print-note/(:num)']           = 'AcctSavingsCashMutation/printNoteAcctSavingsCashMutation/$1';
$route['savings-cash-mutation/print-validation/(:num)']     = 'AcctSavingsCashMutation/printValidationAcctSavingsCashMutation/$1';
$route['savings-cash-mutation/get-savings-cash-mutation']   = 'AcctSavingsCashMutation/getAcctSavingsCashMutation';
$route['savings-cash-mutation/get-list-core-member']        = 'AcctSavingsCashMutation/getListCoreMember';
$route['savings-cash-mutation/get-kecamatan']               = 'AcctSavingsCashMutation/getCoreKecamatan';
$route['savings-cash-mutation/get-mutation-function']       = 'AcctSavingsCashMutation/getMutationFunction';
$route['savings-cash-mutation/get-list-savings-account']    = 'AcctSavingsCashMutation/getListAcctSavingsAccount';
$route['savings-cash-mutation/process-add']                 = 'AcctSavingsCashMutation/processAddAcctSavingsCashMutation';
$route['savings-cash-mutation/process-void']                = 'AcctSavingsCashMutation/processVoidAcctSavingsCashMutation';
$route['savings-cash-mutation/filter']                      = 'AcctSavingsCashMutation/filter';
$route['savings-cash-mutation/filter-list-saving-account']  = 'AcctSavingsCashMutation/filterListAcctSavingsAccount';
$route['savings-cash-mutation/validation/(:num)']           = 'AcctSavingsCashMutation/validationAcctSavingsCashMutation/$1';


//ACCT CREDDITS ACCOUNT ACQUITTANCE
$route['credits-acquittance']                               = 'AcctCreditsAcquittance';
$route['credits-acquittance/add']                           = 'AcctCreditsAcquittance/addAcctCreditsAcquittance';
$route['credits-acquittance/reset']                         = 'AcctCreditsAcquittance/reset';
$route['credits-acquittance/filter']                        = 'AcctCreditsAcquittance/filter';
$route['credits-acquittance/process-void']                  = 'AcctCreditsAcquittance/processVoidAcctSavingsCashMutation';
$route['credits-acquittance/process-add']                   = 'AcctCreditsAcquittance/processAddAcctCreditsAcquittance';
$route['credits-acquittance/akad-list-tunai']               = 'AcctCreditsAcquittance/akadlisttunai';
$route['credits-acquittance/get-credits-acquittance']       = 'AcctCreditsAcquittance/getAcctCreditsAcquittance';
$route['credits-acquittance/add/(:num)']                    = 'AcctCreditsAcquittance/addAcctCreditsAcquittance/$1';
$route['credits-acquittance/print-note/(:num)']             = 'AcctCreditsAcquittance/printNote/$1';

//ACCT CREDDITS PAYMENT SUSPEND getAcctCreditsPaymentSuspend
$route['credits-payment-suspend']                               = 'AcctCreditsPaymentSuspend';
$route['credits-payment-suspend/filter']                        = 'AcctCreditsPaymentSuspend/filter';
$route['credits-payment-suspend/add']                           = 'AcctCreditsPaymentSuspend/addAcctCreditsPaymentSuspend';
$route['credits-payment-suspend/add-cash-payment']              = 'AcctCreditsPaymentSuspend/addAcctCashPayment';
$route['credits-payment-suspend/process-add']                   = 'AcctCreditsPaymentSuspend/processAddAcctCreditsPaymentSuspend';
$route['credits-payment-suspend/reset']                         = 'AcctCreditsPaymentSuspend/reset';
$route['credits-payment-suspend/get-credits-payment-suspend']   = 'AcctCreditsPaymentSuspend/getAcctCreditsPaymentSuspend';
$route['credits-payment-suspend/akad-list-tunai']               = 'AcctCreditsPaymentSuspend/akadlisttunai';
$route['credits-payment-suspend/add/(:num)']                    = 'AcctCreditsPaymentSuspend/addAcctCreditsPaymentSuspend/$1';
$route['credits-payment-suspend/print-schedule/(:num)']         = 'AcctCreditsPaymentSuspend/printScheduleCreditsPayment/$1';


//ACCT CREDDIT ACCOUNT MASTER DATA AcctCreditsAccountMasterData
$route['credits-account-master-data']                  = 'AcctCreditsAccountMasterData';
$route['credits-account-master-data/get-list']         = 'AcctCreditsAccountMasterData/getAcctCreditsAccountMasterDataList';
$route['credits-account-master-data/filter']           = 'AcctCreditsAccountMasterData/filter';
$route['credits-account-master-data/export']           = 'AcctCreditsAccountMasterData/exportAcctCreditsAccountMasterData';
$route['credits-account-master-data/reset-search']     = 'AcctCreditsAccountMasterData/reset_search';


//ACCT CREDDIT AGUNAN AcctCreditsAgunan
$route['credits-agunan']                        = 'AcctCreditsAgunan';
$route['credits-agunan/reset-search']           = 'AcctCreditsAgunan/reset_search';
$route['credits-agunan/export']                 = 'AcctCreditsAgunan/export';
$route['credits-agunan/filter']                 = 'AcctCreditsAgunan/filter';
$route['credits-agunan/get-list']               = 'AcctCreditsAgunan/getAcctCreditsAgunanList';
$route['credits-agunan/edit/(:num)']            = 'AcctCreditsAgunan/edit/$1';
$route['credits-agunan/process-edit']           = 'AcctCreditsAgunan/processEdit';
$route['credits-agunan/update-status/(:num)']   = 'AcctCreditsAgunan/updateAgunanStatus/$1';
$route['credits-agunan/print-receipt/(:num)']   = 'AcctCreditsAgunan/printAgunanReceipt/$1';

//ACCT CASH PAYMENTS
$route['cash-payments/ind-cash-payment']             = 'AcctCashPayments/indAcctCashPayment';
$route['cash-payments/ind-cash-less-payment']        = 'AcctCashPayments/indCashLessPayment';
$route['cash-payments/reset']                        = 'AcctCashPayments/reset';
$route['cash-payments/reset-cash-less']              = 'AcctCashPayments/reset_cashless';
$route['cash-payments/filter']                       = 'AcctCashPayments/filter';
$route['cash-payments/filter-cash-less']             = 'AcctCashPayments/filteracctcashlesspayment';
$route['cash-payments/filter-cash-payment']          = 'AcctCashPayments/filteracctcashpayment';
$route['cash-payments/add']                          = 'AcctCashPayments/addAcctCashPayment';
$route['cash-payments/add/(:num)']                   = 'AcctCashPayments/addAcctCashPayment/$1';
$route['cash-payments/add-cash-less']                = 'AcctCashPayments/addCashlessPayment';
$route['cash-payments/add-cash-less/(:num)']         = 'AcctCashPayments/addCashlessPayment/$1';
$route['cash-payments/add-cash-less/(:num)/(:num)']  = 'AcctCashPayments/addCashlessPayment/$1/$1';
$route['cash-payments/process-add']                  = 'AcctCashPayments/processAddAcctCashPayment';
$route['cash-payments/process-cash-payment']         = 'AcctCashPayments/AcctCashPaymentsProcess';
$route['cash-payments/akad-list-tunai']              = 'AcctCashPayments/akadlisttunai';
$route['cash-payments/get']                          = 'AcctCashPayments/getAcctCashPayment';
$route['cash-payments/get-cash-less']                = 'AcctCashPayments/getAcctCashLessPayment';
$route['cash-payments/get-credit-account-detail']    = 'AcctCashPayments/getCreditAccountDetail';
$route['cash-payments/get-detail-payment']           = 'AcctCashPayments/getDetailPayment';
$route['cash-payments/print-note/(:num)']            = 'AcctCashPayments/printNoteCashPayment/$1';
$route['cash-payments/print-note-less/(:num)']       = 'AcctCashPayments/printNoteCashLessPayment/$1';
$route['cash-payments/history-payment/(:num)']       = 'AcctCashPayments/historyPayment/$1';
$route['cash-payments/credit-list']                  = 'AcctCashPayments/creditList';
$route['cash-payments/akad-list']                    = 'AcctCashPayments/akadlist';
$route['cash-payments/akad-list/(:num)']             = 'AcctCashPayments/akadlist/$1';
$route['cash-payments/akad-list/(:num)']             = 'AcctCashPayments/akadlist/$1/$1';
$route['cash-payments/simpan-list']                  = 'AcctCashPayments/simpananlist';
$route['cash-payments/simpan-list/(:num)']           = 'AcctCashPayments/simpananlist/$1';
$route['cash-payments/simpan-list/(:num)/(:num)']    = 'AcctCashPayments/simpananlist/$1/$1';

//ACCT BANK PAYMENTS
$route['bank-payments/ind-bank-payment']                        = 'AcctBankPayments/indAcctBankPayment';
$route['bank-payments/ind-cash-less-payment']                   = 'AcctBankPayments/indCashLessPayment';
$route['bank-payments/get-detail']                              = 'AcctBankPayments/getCreditAccountDetail';
$route['bank-payments/get-detail-payment']                      = 'AcctBankPayments/getDetailPayment';
$route['bank-payments/reset']                                   = 'AcctBankPayments/reset';
$route['bank-payments/add']                                     = 'AcctBankPayments/addAcctBankPayment';
$route['bank-payments/add-cash-less-payment']                   = 'AcctBankPayments/addCashlessPayment';
$route['bank-payments/add-cash-less-payment/(:num)']            = 'AcctBankPayments/addCashlessPayment/$1';
$route['bank-payments/add-cash-less-payment/(:num)/(:num)']     = 'AcctBankPayments/addCashlessPayment/$1/$1';
$route['bank-payments/add/(:num)']                              = 'AcctBankPayments/addAcctBankPayment/$1';
$route['bank-payments/process-add']                             = 'AcctBankPayments/processAddAcctBankPayment';
$route['bank-payments/get']                                     = 'AcctBankPayments/getAcctBankPayment';
$route['bank-payments/filter-bank-payment']                     = 'AcctBankPayments/filterAcctBankPayment';
$route['bank-payments/akad-list-tunai']                         = 'AcctBankPayments/akadlisttunai';
$route['bank-payments/history-payment/(:num)']                  = 'AcctBankPayments/historyPayment/$1';
$route['bank-payments/print-note/(:num)']                       = 'AcctBankPayments/printNoteBankPayment/$1';
$route['bank-payments/print-note-cash-less/(:num)']             = 'AcctBankPayments/printNoteCashLessPayment/$1';



//Item Category
$route['item-category/add']                  = 'InvtItemCategory/addInvtItemCategory';
$route['item-category/edit/(:num)']          = 'InvtItemCategory/editInvtItemCategory/$1';
$route['item-category/delete/(:num)']        = 'InvtItemCategory/deleteInvtItemCategory/$1';
$route['item-category/process-add']          = 'InvtItemCategory/processAddInvtItemCategory';
$route['item-category/process-edit']         = 'InvtItemCategory/processEditInvtItemCategory';
$route['item-category/elements-add']         = 'InvtItemCategory/function_elements_add';
$route['item-category/reset-add']            = 'InvtItemCategory/reset_add';
$route['item-category/reset-edit/(:num)']    = 'InvtItemCategory/reset_edit/$1';

/* CORE COMPANY */
$route['company'] 			                = 'CoreCompany';
$route['company/add'] 			            = 'CoreCompany/addCoreCompany';
$route['company/edit/(:num)']	            = 'CoreCompany/editCoreCompany/$1';
$route['company/delete/(:num)']	            = 'CoreCompany/deleteCoreCompany/$1';
$route['company/process-add'] 	            = 'CoreCompany/processAddCoreCompany';
$route['company/process-edit'] 	            = 'CoreCompany/processEditCoreCompany';
$route['company/get-company-name'] 	        = 'CoreCompany/getCompanyName';
$route['company/elements-add'] 	            = 'CoreCompany/function_elements_add';
$route['company/state-add'] 	            = 'CoreCompany/function_state_add';
$route['company/reset-add'] 	            = 'CoreCompany/reset_add';
$route['company/reset-edit/(:num)'] 	    = 'CoreCompany/reset_edit/$1';

/* CORE MEMBER CLASS */
$route['member-class'] 			                = 'CoreMemberClass';
$route['member-class/add'] 			            = 'CoreMemberClass/addCoreMemberClass';
$route['member-class/edit/(:num)']	            = 'CoreMemberClass/editCoreMemberClass/$1';
$route['member-class/delete/(:num)']	        = 'CoreMemberClass/deleteCoreMemberClass/$1';
$route['member-class/process-add'] 	            = 'CoreMemberClass/processAddCoreMemberClass';
$route['member-class/process-edit'] 	        = 'CoreMemberClass/processEditCoreMemberClass';
$route['member-class/get-member-class-name'] 	= 'CoreMemberClass/getMemberClassName';
$route['member-class/elements-add'] 	        = 'CoreMemberClass/function_elements_add';
$route['member-class/reset-add'] 	            = 'CoreMemberClass/reset_add';
$route['member-class/reset-edit/(:num)'] 	    = 'CoreMemberClass/reset_edit/$1';


/* CORE BRANCH */
$route['branch'] 			                = 'CoreBranch';
$route['branch/add'] 			            = 'CoreBranch/addCoreBranch';
$route['branch/edit/(:num)']	            = 'CoreBranch/editCoreBranch/$1';
$route['branch/delete/(:num)']	            = 'CoreBranch/deleteCoreBranch/$1';
$route['branch/process-add'] 	            = 'CoreBranch/processAddCoreBranch';
$route['branch/process-edit'] 	            = 'CoreBranch/processEditCoreBranch';
$route['branch/get-branch-name'] 	        = 'CoreBranch/getBranchName';
$route['branch/elements-add'] 	            = 'CoreBranch/function_elements_add';
$route['branch/reset-add'] 	                = 'CoreBranch/reset_add';
$route['branch/reset-edit/(:num)'] 	        = 'CoreBranch/reset_edit/$1';

// ACCT SAVINGS ACCOUNT DETAIL AcctSavingsAccountDetail
$route['savings-account-detail/show-detail'] 	                    = 'AcctSavingsAccountDetail/showdetail';
$route['savings-account-detail/filter'] 	                        = 'AcctSavingsAccountDetail/filter';
$route['savings-account-detail/reset_search'] 	                    = 'AcctSavingsAccountDetail/reset_search';
$route['savings-account-detail/get-list-savings-account'] 	        = 'AcctSavingsAccountDetail/getListAcctSavingsAccount';
$route['savings-account-detail/show-detail/(:num)'] 	            = 'AcctSavingsAccountDetail/showdetail/$1';

// ACCT ACCOUNT AcctAccount
$route['account'] 			                = 'AcctAccount';
$route['account/export-account']     		= 'AcctAccount/exportAcctAccount';
$route['account/add'] 			            = 'AcctAccount/addAcctAccount';
$route['account/edit/(:num)'] 			    = 'AcctAccount/editAcctAccount/$1';
$route['account/process-edit'] 			    = 'AcctAccount/processEditAcctAccount';
$route['account/process-add'] 			    = 'AcctAccount/processAddAcctAccount';
$route['account/delete/(:num)'] 			= 'AcctAccount/deleteAcctAccount/$1';

// ACCT AcctJournalVoucher AcctJournalVoucher
$route['journal-voucher'] 			                = 'AcctJournalVoucher';
$route['journal-voucher/add'] 			            = 'AcctJournalVoucher/addAcctJournalVoucher';
$route['journal-voucher/elements-add'] 			    = 'AcctJournalVoucher/function_elements_add';
$route['journal-voucher/reset-data'] 			    = 'AcctJournalVoucher/reset_data';
$route['journal-voucher/reset-search'] 			    = 'AcctJournalVoucher/reset_search';
$route['journal-voucher/process-add-array'] 		= 'AcctJournalVoucher/processAddArrayAcctjournalVoucher';
$route['journal-voucher/process-add'] 		        = 'AcctJournalVoucher/processAddAcctJournalVoucher';
$route['journal-voucher/filter'] 		            = 'AcctJournalVoucher/filter';
$route['journal-voucher/process-printing/(:num)'] 	= 'AcctJournalVoucher/processPrinting/$1';

// ACCT AcctJournalVoucher AcctJournalVoucher
$route['memorial-journal']                          = 'AcctMemorialJournal';
$route['memorial-journal/reset-search']             = 'AcctMemorialJournal/reset_search';
$route['memorial-journal/filter']                   = 'AcctMemorialJournal/filter';
$route['memorial-journal/print-validation/(:num)']  = 'AcctMemorialJournal/printValidationAcctMemorialJournal/$1';


// ACCT AcctJournalVoucher AcctJournalVoucher
$route['ledger-report/cash-teller-report']                      = 'AcctLedgerReport/cashTellerReport';
$route['ledger-report/process-printing']                        = 'AcctLedgerReport/processPrinting';
$route['ledger-report/process-printing-teller-report']          = 'AcctLedgerReport/processPrintingCashTellerReport';

// ACCT AcctJournalVoucher AcctJournalVoucher
$route['general-ledger-report']                                 = 'AcctGeneralLedgerReport';
$route['general-ledger-report/reset-data']                      = 'AcctGeneralLedgerReport/reset_data';
$route['general-ledger-report/reset-search']                    = 'AcctGeneralLedgerReport/reset_search';
$route['general-ledger-report/filter']                          = 'AcctGeneralLedgerReport/filter';
$route['general-ledger-report/process-printing']                = 'AcctGeneralLedgerReport/processPrinting';
$route['general-ledger-report/export/(:num)/(:num)']            = 'AcctGeneralLedgerReport/export/$1/$1';
$route['general-ledger-report/pdf/(:num)/(:num)']               = 'AcctGeneralLedgerReport/pdf/$1/$1';

 
//Savings AcctSavings
$route['savings'] 			                = 'AcctSavings';
$route['savings/add'] 			            = 'AcctSavings/addAcctSavings';
$route['savings/state-add'] 			    = 'AcctSavings/function_state_add';
$route['savings/elements-add'] 			    = 'AcctSavings/function_elements_add';
$route['savings/reset-data'] 			    = 'AcctSavings/reset_data';
$route['savings/process-add-account'] 		= 'AcctSavings/processAddAcctAccount';
$route['savings/process-add'] 		        = 'AcctSavings/processAddAcctSavings';
$route['savings/process-edit'] 			    = 'AcctSavings/processEditAcctSavings';
$route['savings/edit/(:num)'] 			    = 'AcctSavings/editAcctSavings/$1';
$route['savings/delete/(:num)'] 			= 'AcctSavings/deleteAcctSavings/$1';


//deposito AcctDeposito
$route['deposito'] 			                    = 'AcctDeposito';
$route['deposito/add'] 			                = 'AcctDeposito/addAcctDeposito';
$route['deposito/elements-add'] 			    = 'AcctDeposito/function_elements_add';
$route['deposito/state-add'] 			        = 'AcctDeposito/function_state_add';
$route['deposito/reset-data'] 			        = 'AcctDeposito/reset_data';
$route['deposito/process-add-account'] 		    = 'AcctDeposito/processAddAcctAccount';
$route['deposito/process-add'] 		            = 'AcctDeposito/processAddAcctDeposito';
$route['deposito/process-edit'] 			    = 'AcctDeposito/processEditAcctDeposito';
$route['deposito/edit/(:num)'] 			        = 'AcctDeposito/editAcctDeposito/$1';
$route['deposito/delete/(:num)'] 			    = 'AcctDeposito/deleteAcctDeposito/$1';

//credits AcctCredits
$route['credits'] 			                    = 'AcctCredits';
$route['credits/add'] 			                = 'AcctCredits/addAcctCredits';
$route['credits/elements-add'] 			        = 'AcctCredits/function_elements_add';
$route['credits/state-add'] 			        = 'AcctCredits/function_state_add';
$route['credits/reset-data'] 			        = 'AcctCredits/reset_data';
$route['credits/process-add-account'] 		    = 'AcctCredits/processAddAcctAccount';
$route['credits/process-add'] 		            = 'AcctCredits/processAddAcctCredits';
$route['credits/process-edit'] 			        = 'AcctCredits/processEditAcctCredits';
$route['credits/edit/(:num)'] 			        = 'AcctCredits/editAcctCredits/$1';
$route['credits/delete/(:num)'] 			    = 'AcctCredits/deleteAcctCredits/$1';

//office CoreOffice
$route['office'] 			                    = 'CoreOffice';
$route['office/add'] 			                = 'CoreOffice/addCoreOffice';
$route['office/add-dusun'] 			            = 'CoreOffice/addCoreDusun';
$route['office/elements-add'] 			        = 'CoreOffice/function_elements_add';
$route['office/elements-edit'] 			        = 'CoreOffice/function_elements_edit';
$route['office/state-add'] 			            = 'CoreOffice/function_state_add';
$route['office/reset-data'] 			        = 'CoreOffice/reset_data';
$route['office/process-add-account'] 		    = 'CoreOffice/processAddAcctAccount';
$route['office/process-add'] 		            = 'CoreOffice/processAddCoreOffice';
$route['office/process-edit'] 			        = 'CoreOffice/processEditCoreOffice';
$route['office/edit/(:num)'] 			        = 'CoreOffice/editCoreOffice/$1';
$route['office/edit-dusun'] 			        = 'CoreOffice/editCoreDusun';
$route['office/delete/(:num)'] 			        = 'CoreOffice/deleteCoreOffice/$1';
$route['office/delete-dusun/(:num)'] 			= 'CoreOffice/deleteCoreDusun/$1';
$route['office/delete-edit-dusun/(:num)/(:num)']= 'CoreOffice/deleteEditCoreDusun/$1/$1';
$route['office/get-kecamatan/(:num)'] 			= 'CoreOffice/getCoreKecamatan/$1';
$route['office/get-kelurahan/(:num)'] 			= 'CoreOffice/getCoreKelurahan/$1';

//configuration collectibility ConfigurationCollectibility
$route['configuration-collectibility'] 			                    = 'ConfigurationCollectibility';
$route['configuration-collectibility/add'] 			                = 'ConfigurationCollectibility/addConfigurationCollectibility';
$route['configuration-collectibility/elements-add'] 			    = 'ConfigurationCollectibility/function_elements_add';
$route['configuration-collectibility/state-add'] 			        = 'ConfigurationCollectibility/function_state_add';
$route['configuration-collectibility/reset-data'] 			        = 'ConfigurationCollectibility/reset_data';
$route['configuration-collectibility/process-add-account'] 		    = 'ConfigurationCollectibility/processAddAcctAccount';
$route['configuration-collectibility/process-add'] 		            = 'ConfigurationCollectibility/processAddConfigurationCollectibility';
$route['configuration-collectibility/process-edit'] 			    = 'ConfigurationCollectibility/processEditConfigurationCollectibility';
$route['configuration-collectibility/edit/(:num)'] 			        = 'ConfigurationCollectibility/editConfigurationCollectibility/$1';
$route['configuration-collectibility/delete/(:num)'] 			    = 'ConfigurationCollectibility/deleteConfigurationCollectibility/$1';


//Mutation AcctMutation
$route['mutation'] 			                    = 'AcctMutation';
$route['mutation/add'] 			                = 'AcctMutation/addAcctMutation';
$route['mutation/elements-add'] 			    = 'AcctMutation/function_elements_add';
$route['mutation/state-add'] 			        = 'AcctMutation/function_state_add';
$route['mutation/reset-data'] 			        = 'AcctMutation/reset_data';
$route['mutation/process-add'] 		            = 'AcctMutation/processAddAcctMutation';
$route['mutation/process-edit'] 			    = 'AcctMutation/processEditAcctMutation';
$route['mutation/edit/(:num)'] 			        = 'AcctMutation/editAcctMutation/$1';
$route['mutation/delete/(:num)'] 			    = 'AcctMutation/deleteAcctMutation/$1';

//Mutation AcctMutation
$route['mutation'] 			                    = 'AcctMutation';
$route['mutation/add'] 			                = 'AcctMutation/addAcctMutation';
$route['mutation/elements-add'] 			    = 'AcctMutation/function_elements_add';
$route['mutation/state-add'] 			        = 'AcctMutation/function_state_add';
$route['mutation/reset-data'] 			        = 'AcctMutation/reset_data';
$route['mutation/process-add'] 		            = 'AcctMutation/processAddAcctMutation';
$route['mutation/process-edit'] 			    = 'AcctMutation/processEditAcctMutation';
$route['mutation/edit/(:num)'] 			        = 'AcctMutation/editAcctMutation/$1';
$route['mutation/delete/(:num)'] 			    = 'AcctMutation/deleteAcctMutation/$1';

//source fund AcctSourceFund
$route['source-fund'] 			                    = 'AcctSourceFund';
$route['source-fund/add'] 			                = 'AcctSourceFund/addAcctSourceFund';
$route['source-fund/elements-add'] 			        = 'AcctSourceFund/function_elements_add';
$route['source-fund/state-add'] 			        = 'AcctSourceFund/function_state_add';
$route['source-fund/reset-data'] 			        = 'AcctSourceFund/reset_data';
$route['source-fund/process-add'] 		            = 'AcctSourceFund/processAddAcctSourceFund';
$route['source-fund/process-edit'] 			        = 'AcctSourceFund/processEditAcctSourceFund';
$route['source-fund/edit/(:num)'] 			        = 'AcctSourceFund/editAcctSourceFund/$1';
$route['source-fund/delete/(:num)'] 			    = 'AcctSourceFund/deleteAcctSourceFund/$1';


//dusun CoreDusun
$route['dusun'] 			                    = 'CoreDusun';
$route['dusun/add'] 			                = 'CoreDusun/addCoreDusun';
$route['dusun/filter'] 			                = 'CoreDusun/filter';
$route['dusun/elements-add'] 			        = 'CoreDusun/function_elements_add';
$route['dusun/state-add'] 			            = 'CoreDusun/function_state_add';
$route['dusun/reset-data'] 			            = 'CoreDusun/reset_data';
$route['dusun/process-add'] 		            = 'CoreDusun/processAddCoreDusun';
$route['dusun/process-edit'] 			        = 'CoreDusun/processEditCoreDusun';
$route['dusun/edit/(:num)'] 			        = 'CoreDusun/editCoreDusun/$1';
$route['dusun/delete/(:num)'] 			        = 'CoreDusun/deleteCoreDusun/$1';
$route['dusun/get-kecamatan'] 			        = 'CoreDusun/getCoreKecamatan/$1';
$route['dusun/get-kelurahan'] 			        = 'CoreDusun/getCoreKelurahan/$1';


//preference income PreferenceIncome
$route['preference-income'] 			                    = 'PreferenceIncome';
$route['preference-income/add'] 			                = 'PreferenceIncome/addPreferenceIncome';
$route['preference-income/process-add-account'] 		    = 'PreferenceIncome/processAddAcctAccount';
$route['preference-income/process-add'] 		            = 'PreferenceIncome/processAddPreferenceIncome';
$route['preference-income/process-edit'] 			        = 'PreferenceIncome/processEditPreferenceIncome';
$route['preference-income/edit/(:num)'] 			        = 'PreferenceIncome/editPreferenceIncome/$1';
$route['preference-income/delete/(:num)'] 			        = 'PreferenceIncome/deletePreferenceIncome/$1';

//bank account AcctBankAccount
$route['bank-account'] 			                        = 'AcctBankAccount';
$route['bank-account/add'] 			                    = 'AcctBankAccount/addAcctBankAccount';
$route['bank-account/process-add-account'] 		        = 'AcctBankAccount/processAddAcctAccount';
$route['bank-account/process-add'] 		                = 'AcctBankAccount/processAddAcctBankAccount';
$route['bank-account/process-edit'] 			        = 'AcctBankAccount/processEditAcctBankAccount';
$route['bank-account/edit/(:num)'] 			            = 'AcctBankAccount/editAcctBankAccount/$1';
$route['bank-account/delete/(:num)'] 			        = 'AcctBankAccount/deleteAcctBankAccount/$1';


//bank account EmptyData
$route['empty-data'] 			                        = 'EmptyData';
$route['empty-data/process-empty-data'] 		        = 'EmptyData/processEmptyData';

//bank account EmptyData 
$route['end-of-days/open-branch'] 			            = 'SystemEndOfDays/OpenBranch';
$route['end-of-days/process-open-branch'] 			    = 'SystemEndOfDays/ProcessOpenBranch';
$route['end-of-days/close-branch'] 		                = 'SystemEndOfDays/CloseBranch';
$route['end-of-days/process-close-branch'] 		        = 'SystemEndOfDays/ProcessCloseBranch';

//AcctNominativeRecap
$route['nominative/rekap'] 			                    = 'AcctNominativeRecapReport';
$route['nominative-rekap'] 			                    = 'AcctNominativeRecapReport';
$route['nominative-rekap/viewreport'] 			        = 'AcctNominativeRecapReport/viewreport';

//AcctBalanceSheetReportNew1 
$route['balance-sheet'] 			                    = 'AcctBalanceSheetReportNew1';

//AcctProfitLossReportNew1
$route['profit-loss'] 			                        = 'AcctProfitLossReportNew1';

//AcctBalanceSheetComparationReportNew 
$route['balance-sheet-comparation'] 			        = 'AcctBalanceSheetComparationReportNew';

//AcctProfitLossComparationReport 
$route['profit-loss-comparation'] 			            = 'AcctProfitLossComparationReport';


//AcctFinancialAnalysisReport   
$route['fincancial-analysis'] 			                = 'AcctFinancialAnalysisReport';

//UserGroup
$route['user-group'] 			                        = 'UserGroup';
$route['user-group/add'] 			                    = 'UserGroup/Add';
$route['user-group/process-add'] 			            = 'UserGroup/processAddUserGroup';
$route['user-group/process-edit'] 			            = 'UserGroup/processEditUserGroup';
$route['user-group/edit/(:num)'] 			            = 'UserGroup/Edit/$1';
$route['user-group/delete/(:num)'] 			            = 'UserGroup/delete/$1';

//AcctSavingsProfitSharingNew 
$route['savings-profit-sharing'] 			            = 'AcctSavingsProfitSharingNew';
$route['savings-profit-sharing/list-data'] 			    = 'AcctSavingsProfitSharingNew/listdata';
$route['savings-profit-sharing/process-add'] 		    = 'AcctSavingsProfitSharingNew/processAddAcctSavingsProfitSharing';
$route['savings-profit-sharing/process-update'] 		= 'AcctSavingsProfitSharingNew/processUpdateAcctSavingsProfitSharing';
$route['savings-profit-sharing/recalculate/(:num)'] 	= 'AcctSavingsProfitSharingNew/recalculate/$1';

//AcctSavingsAccountBlockir 
$route['savings-account-blockir'] 			            = 'AcctSavingsAccountBlockir';
$route['savings-account-blockir/unblockir'] 			= 'AcctSavingsAccountBlockir/unBlockirAcctSavingsAccount';
$route['savings-account-blockir/add'] 			        = 'AcctSavingsAccountBlockir/addAcctSavingsAccountBlockir';
$route['savings-account-blockir/process-add-unblockir'] = 'AcctSavingsAccountBlockir/processAddAcctSavingsAccountUnBlockir';
$route['savings-account-blockir/process-add-blockir']   = 'AcctSavingsAccountBlockir/processAddAcctSavingsAccountBlockir';
$route['savings-account-blockir/add-unblockir/(:num)'] 	= 'AcctSavingsAccountBlockir/addAcctSavingsAccountUnBlockir/$1';
$route['savings-account-blockir/get-list'] 			    = 'AcctSavingsAccountBlockir/getAcctSavingsAccountBlockirList';
$route['savings-account-blockir/function-elements-edit']= 'AcctSavingsAccountBlockir/function_elements_edit';
$route['savings-account-blockir/reset-edit/(:num)']     = 'AcctSavingsAccountBlockir/reset_edit/$1';

//AcctDepositoAccountBlockir 
$route['deposito-account-blockir'] 			                = 'AcctDepositoAccountBlockir';
$route['deposito-account-blockir/unblockir'] 			    = 'AcctDepositoAccountBlockir/unBlockirAcctDepositoAccount';
$route['deposito-account-blockir/process-add-unblockir'] 	= 'AcctDepositoAccountBlockir/processAddAcctDepositoAccountUnBlockir';
$route['deposito-account-blockir/process-add-blockir'] 	    = 'AcctDepositoAccountBlockir/processAddAcctDepositoAccountBlockir';
$route['deposito-account-blockir/add'] 			            = 'AcctDepositoAccountBlockir/addAcctDepositoAccountBlockir';
$route['deposito-account-blockir/add-unblockir/(:num)'] 	= 'AcctDepositoAccountBlockir/addAcctDepositoAccountUnBlockir/$1';
$route['deposito-account-blockir/get-list'] 			    = 'AcctDepositoAccountBlockir/getAcctDepositoAccountBlockirList';
$route['deposito-account-blockir/get-list-unblockir'] 	    = 'AcctDepositoAccountBlockir/getAcctDepositoAccountUnBlockirList';
$route['deposito-account-blockir/function-elements-edit']   = 'AcctDepositoAccountBlockir/function_elements_edit';
$route['deposito-account-blockir/reset-edit/(:num)']        = 'AcctDepositoAccountBlockir/reset_edit/$1';

//AcctSavingsCloseBook
$route['savings-close-book'] 			                    = 'AcctSavingsCloseBook';
$route['savings-close-book/process-add'] 			        = 'AcctSavingsCloseBook/processAddAcctSavingsCloseBook';

//AcctSavingsAccountUtility
$route['savings-account-utility'] 			                = 'AcctSavingsAccountUtility';
$route['savings-account-utility/filter'] 			        = 'AcctSavingsAccountUtility/filter';
$route['savings-account-utility/process-add'] 			    = 'AcctSavingsAccountUtility/processAddAcctSavingsAccountUtility';
$route['savings-account-utility/add'] 			            = 'AcctSavingsAccountUtility/addAcctSavingsAccountUtility';
$route['savings-account-utility/add/(:num)'] 			    = 'AcctSavingsAccountUtility/addAcctSavingsAccountUtility/$1';
$route['savings-account-utility/print-validation/(:num)'] 	= 'AcctSavingsAccountUtility/printValidationAcctSavingsAccountUtility/$1';
$route['savings-account-utility/validation/(:num)'] 	    = 'AcctSavingsAccountUtility/validationAcctSavingsAccountUtility/$1';
$route['savings-account-utility/print-note/(:num)'] 	    = 'AcctSavingsAccountUtility/printNoteAcctSavingsAccountUtility/$1';
$route['savings-account-utility/get-master'] 			    = 'AcctSavingsAccountUtility/getMasterDataSavingsAccount';
$route['savings-account-utility/get-list'] 			        = 'AcctSavingsAccountUtility/getAcctSavingsAccountUtilityList';
$route['savings-account-utility/function-elements-add'] 	= 'AcctSavingsAccountUtility/function_elements_add';
$route['savings-account-utility/reset-data'] 	            = 'AcctSavingsAccountUtility/reset_data';
$route['savings-account-utility/get-savings-account-no'] 	= 'AcctSavingsAccountUtility/getSavingsAccountNo';
$route['savings-account-utility/get-list-core-member']  	= 'AcctSavingsAccountUtility/getListCoreMember';

//AcctNominativeSavingsPickup
$route['nominative-savings-pickup'] 			            = 'AcctNominativeSavingsPickup';
$route['nominative-savings-pickup/function-elements-add'] 	= 'AcctNominativeSavingsPickup/function_elements_add';
$route['nominative-savings-pickup/process-val-nominative'] 	= 'AcctNominativeSavingsPickup/processValAcctNominativeSavingsPickup';
$route['nominative-savings-pickup/show-detail/(:num)'] 		= 'AcctNominativeSavingsPickup/showdetail/$1';

//AcctPaymentPrintMutation 
$route['payment-print-mutation/ind-payment'] 	                    = 'AcctPaymentPrintMutation/indPayment';
$route['payment-print-mutation/ind-payment/(:num)'] 	            = 'AcctPaymentPrintMutation/indPayment/$1';
$route['payment-print-mutation/reset-search'] 	                    = 'AcctPaymentPrintMutation/reset_search';
$route['payment-print-mutation/filter'] 	                        = 'AcctPaymentPrintMutation/filter';
$route['payment-print-mutation/process-printing/preview/(:num)'] 	= 'AcctPaymentPrintMutation/processPrinting/$1';
$route['payment-print-mutation/process-printing/print/(:num)'] 	= 'AcctPaymentPrintMutation/processPrinting/$1';


//CoreMemberReport 
$route['member-report']     			            = 'CoreMemberReport';
$route['member-report/viewport']     			    = 'CoreMemberReport/viewreport';

//AcctNominativeSavingsReport
$route['nominative-savings-report']     			            = 'AcctNominativeSavingsReport';
$route['nominative-savings-report/viewreport']                  = 'AcctNominativeSavingsReport/viewreport';


//AcctNominativeDepositoReport
$route['nominative-deposito-report']     			            = 'AcctNominativeDepositoReport';
$route['nominative-deposito-report/viewreport']                 = 'AcctNominativeDepositoReport/viewreport';

//AcctNominativeCreditsReport 
$route['nominative-credits-report']     			           = 'AcctNominativeCreditsReport';
$route['nominative-credits-report/viewreport']                 = 'AcctNominativeCreditsReport/viewreport';

//AcctSavingsAccountOfficerReport 
$route['savings-account-officer-report']     			       = 'AcctSavingsAccountOfficerReport';
$route['savings-account-officer-report/viewreport']            = 'AcctSavingsAccountOfficerReport/viewreport';

//AcctDepositoAccountOfficerReport  
$route['deposito-account-officer-report']      			       = 'AcctDepositoAccountOfficerReport';
$route['deposito-account-officer-report/viewreport']           = 'AcctDepositoAccountOfficerReport/viewreport';


//AcctCreditsAccountOfficerReport  
$route['credits-account-officer-report']      			       = 'AcctCreditsAccountOfficerReport';
$route['credits-account-officer-report/viewreport']            = 'AcctCreditsAccountOfficerReport/viewreport';

//AcctSavingsProfitSharingReport  
$route['savings-profit-sharing-report']      			       = 'AcctSavingsProfitSharingReport';

//AcctDepositoProfitSharingReport  
$route['deposito-profit-sharing-report']      			       = 'AcctDepositoProfitSharingReport';

//AcctSavingsDailyTransferMutation 
$route['savings-daily-transfer-mutation']      			       = 'AcctSavingsDailyTransferMutation';

//AcctSavingsMandatoryHasntPaidReport
$route['savings-mandatory-hasnt-paid-report']      			   = 'AcctSavingsMandatoryHasntPaidReport';
$route['savings-mandatory-hasnt-paid-report/viewreport']       = 'AcctSavingsMandatoryHasntPaidReport/viewreport';

//AcctDepositoAccountClosedReport
$route['deposito-account-closed-report']      			        = 'AcctDepositoAccountClosedReport';
$route['deposito-account-closed-report/viewreport']             = 'AcctDepositoAccountClosedReport/viewreport';

//AcctCreditsPaymentReport
$route['credits-payment-report']      			                = 'AcctCreditsPaymentReport';
$route['credits-payment-report/viewreport']                     = 'AcctCreditsPaymentReport/viewreport';

//AcctCreditsPaymentDailyReport
$route['credits-payment-daily-report']      			        = 'AcctCreditsPaymentDailyReport';
$route['credits-payment-daily-report/viewreport']               = 'AcctCreditsPaymentDailyReport/viewreport';

//AcctCreditsHasntPaidReport 
$route['credits-hasnt-paid-report']      			               = 'AcctCreditsHasntPaidReport';
$route['credits-hasnt-paid-report/viewreport']                     = 'AcctCreditsHasntPaidReport/viewreport';

//AcctCreditsPaymentDueReport 
$route['credits-payment-due-report']      			               = 'AcctCreditsPaymentDueReport';
$route['credits-payment-due-report/viewreport']                    = 'AcctCreditsPaymentDueReport/viewreport';

$route['credits-payment-due-paid-report']                          = 'AcctCreditsPaymentDuePaidReport';
$route['credits-payment-due-paid-report/viewreport']               = 'AcctCreditsPaymentDuePaidReport/viewreport';

//AcctCreditsRescheduleReport 
$route['credits-reschedule-report']      			               = 'AcctCreditsRescheduleReport';
$route['credits-reschedule-report/viewreport']                     = 'AcctCreditsRescheduleReport/viewreport';

//AcctCreditsCollectibility 
$route['credits-collectibility']      			                   = 'AcctCreditsCollectibility';

//AcctCreditsAccountPaidOff 
$route['credits-account-paid-off']      			               = 'AcctCreditsAccountPaidOff';

//AcctCreditsPaymentSuspendReport 
$route['credits-payment-suspend-report']      			           = 'AcctCreditsPaymentSuspendReport';

//AcctNominativeSavingsReportPickup
$route['nominative-savings-report-pickup']      			       = 'AcctNominativeSavingsReportPickup';
$route['nominative-savings-report-pickup/viewreport']              = 'AcctNominativeSavingsReportPickup/viewreport';

//TaxReport
$route['tax-report']      			                               = 'TaxReport';
$route['tax-report/viewreport']                                    = 'TaxReport/viewreport';

//AcctCashPaymentsBranch  
$route['cash-payments-branch']                    			       = 'AcctCashPaymentsBranch';
$route['cash-payments-branch/ind-cash-payment']                    = 'AcctCashPaymentsBranch/indAcctCashPayment';
$route['cash-payments-branch/print-note-cash-payment/(:num)']      = 'AcctCashPaymentsBranch/printNoteCashPayment/$1';
$route['cash-payments-branch/add-cash-payment/(:num)']             = 'AcctCashPaymentsBranch/addAcctCashPayment/$1';
$route['cash-payments-branch/add-cash-payment']                    = 'AcctCashPaymentsBranch/addAcctCashPayment';
$route['cash-payments-branch/reset-search']                        = 'AcctCashPaymentsBranch/reset_search';
$route['cash-payments-branch/filter-cash-payment']                 = 'AcctCashPaymentsBranch/filteracctcashpayment';
$route['cash-payments-branch/get-cash-payment']                    = 'AcctCashPaymentsBranch/getAcctCashPayment';
$route['cash-payments-branch/process-add-cash-payment']            = 'AcctCashPaymentsBranch/processAddAcctCashPayment';
$route['cash-payments-branch/akad-list-tunai']                     = 'AcctCashPaymentsBranch/akadlisttunai';

//NewMemberReport
$route['new-member-report']      			                       = 'NewMemberReport';
$route['new-member-report/viewreport']                             = 'NewMemberReport/viewreport';

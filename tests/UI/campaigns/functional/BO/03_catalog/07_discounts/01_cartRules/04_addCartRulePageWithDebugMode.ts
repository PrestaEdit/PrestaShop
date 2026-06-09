// Import utils
import testContext from '@utils/testContext';

// Import pages
import migratedCartRulePage from '@pages/BO/catalog/discounts/migratedCartRule';

import {expect} from 'chai';
import {
  boDashboardPage,
  boFeatureFlagPage,
  boLoginPage,
  boPerformancePage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_discounts_cartRules_addCartRulePageWithDebugMode';

/*
Regression test for issue #34737
Enable debug mode
Enable the "Cart rules" experimental feature flag
Open the migrated "Add new cart rule" page and check it renders without error
(the cart rule form used to be rendered twice, which throws in debug mode)
Reset: disable the feature flag and debug mode
 */
describe('BO - Catalog - Discounts : Open the migrated \'Add new cart rule\' page in debug mode', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Advanced Parameters > Performance\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToPerformancePage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.performanceLink,
    );
    await boPerformancePage.closeSfToolBar(page);

    const pageTitle = await boPerformancePage.getPageTitle(page);
    expect(pageTitle).to.contains(boPerformancePage.pageTitle);
  });

  it('should enable debug mode', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableDebugMode', baseContext);

    const successMessage = await boPerformancePage.setDebugMode(page, true);
    expect(successMessage).to.contains(boPerformancePage.successUpdateMessage);
  });

  it('should go to \'Advanced Parameters > New & Experimental Features\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeatureFlagPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.featureFlagLink,
    );
    await boFeatureFlagPage.closeSfToolBar(page);

    const pageTitle = await boFeatureFlagPage.getPageTitle(page);
    expect(pageTitle).to.contains(boFeatureFlagPage.pageTitle);
  });

  it('should enable the "Cart rules" feature flag', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enableCartRuleFeatureFlag', baseContext);

    const successMessage = await boFeatureFlagPage.setFeatureFlag(page, boFeatureFlagPage.featureFlagCartRule, true);
    expect(successMessage).to.contains(boFeatureFlagPage.successfulUpdateMessage);
  });

  it('should go to \'Catalog > Discounts\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.discountsLink,
    );
  });

  it('should open the \'Add new cart rule\' page and check it renders without error', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openAddCartRulePage', baseContext);

    await migratedCartRulePage.goToAddNewCartRulePage(page);

    const isCreatePageVisible = await migratedCartRulePage.isCreatePageVisible(page);
    expect(isCreatePageVisible, 'The "Add new cart rule" page did not render correctly').to.eq(true);
  });

  // Reset: disable the feature flag and debug mode
  it('should disable the "Cart rules" feature flag', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableCartRuleFeatureFlag', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.featureFlagLink,
    );

    const successMessage = await boFeatureFlagPage.setFeatureFlag(page, boFeatureFlagPage.featureFlagCartRule, false);
    expect(successMessage).to.contains(boFeatureFlagPage.successfulUpdateMessage);
  });

  it('should disable debug mode', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'disableDebugMode', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.performanceLink,
    );

    const successMessage = await boPerformancePage.setDebugMode(page, false);
    expect(successMessage).to.contains(boPerformancePage.successfulUpdateMessage);
  });
});

import BOBasePage from '@pages/BO/BObasePage';

import {
  type Page,
} from '@prestashop-core/ui-testing';

/**
 * Migrated cart rules page (displayed when the "cart_rule" experimental feature flag is enabled),
 * contains selectors and functions used to reach and check the migrated "Add new cart rule" page.
 * @class
 * @extends BOBasePage
 */
class MigratedCartRule extends BOBasePage {
  private readonly addNewCartRuleButton: string;

  private readonly createPageContainer: string;

  /**
   * @constructs
   * Setting up selectors to use on the migrated cart rules pages
   */
  constructor() {
    super();

    // Selectors
    // Toolbar button on the migrated cart rules grid (id pattern: page-header-desc-<gridId>-<buttonKey>)
    this.addNewCartRuleButton = '#page-header-desc-cart_rule-add_cart_rule';
    // Wrapper rendered by the migrated create cart rule template
    this.createPageContainer = '#cart-rule-create-page';
  }

  /**
   * Click on the "Add new cart rule" button from the migrated cart rules grid
   * @param page {Page} Browser tab
   * @returns {Promise<void>}
   */
  async goToAddNewCartRulePage(page: Page): Promise<void> {
    await this.clickAndWaitForURL(page, this.addNewCartRuleButton);
  }

  /**
   * Returns whether the migrated "Add new cart rule" page rendered successfully.
   * When the cart rule form is rendered twice (regression of issue #34737), the page returns
   * an error in debug mode and this wrapper is never displayed.
   * @param page {Page} Browser tab
   * @returns {Promise<boolean>}
   */
  async isCreatePageVisible(page: Page): Promise<boolean> {
    return this.elementVisible(page, this.createPageContainer, 2000);
  }
}

export default new MigratedCartRule();

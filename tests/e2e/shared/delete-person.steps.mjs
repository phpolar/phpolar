import {After, Step, When} from '@badeball/cypress-cucumber-preprocessor';
import {fakePerson} from "./fake.person.mjs";

const baseUrl = Cypress.env('baseUrl');

When('I delete a person', function () {
  cy.visit(baseUrl).then(() => {
    cy.contains(fakePerson.firstName)
      .parent('tr')
      .within(() => {
        cy.get('form').submit();
      });
  });
});

After({ tags: '@deletePerson' }, function () {
  Step(this, 'I delete a person');
});

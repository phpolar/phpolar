import {Before, Step, When} from '@badeball/cypress-cucumber-preprocessor';
import {fakePerson} from "./fake.person.mjs";

const baseUrl = Cypress.env('baseUrl');

When('I add a person', function () {
  cy.visit(baseUrl);
  const addPersonLink = cy.get('a[href="/person/form"]');
  addPersonLink.click().then(() => {
    cy.title().should('eq', 'MyApp Person Form');

    cy.get('form').within(() => {
      cy.get('input#firstName').type(fakePerson.firstName);
      cy.get('input#lastName').type(fakePerson.lastName);
      cy.get('input#age').type(fakePerson.age);
      cy.get('input#birthplace').type(fakePerson.birthplace);
      cy.get('input#occupation').type(fakePerson.occupation);
      cy.get('input#notes').type(fakePerson.notes);

      cy.root().submit();
    });
  });
});

Before( { tags: '@addPerson'}, function () {
  Step(this, 'I add a person');
});

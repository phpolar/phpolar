import {Then} from '@badeball/cypress-cucumber-preprocessor';
import {fakePerson} from "./shared/fake.person.mjs";

const baseUrl = Cypress.env('baseUrl');

Then('I should see the person in the people list', function () {
  cy.visit(baseUrl).then(() => {
    cy.title().should('eq',"MyApp People List");
    cy.contains(fakePerson.firstName)
      .parent('tr')
      .within(() => {
        cy.get('td').eq(2).contains(fakePerson.lastName);
        cy.get('td').eq(3).contains(fakePerson.age);
        cy.get('td').eq(4).contains(fakePerson.birthplace);
        cy.get('td').eq(5).contains(fakePerson.occupation);
        cy.get('td').eq(6).contains(fakePerson.notes);
      });
  });
});

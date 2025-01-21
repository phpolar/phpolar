Feature: Add Person
  @deletePerson
  Scenario: adding a person
    When I add a person
    Then I should see the person in the people list
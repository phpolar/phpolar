Feature: Delete a Person
  @addPerson
  Scenario: deleting a person
    When I delete a person
    Then I should not see the person in the people list
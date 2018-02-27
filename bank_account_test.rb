require 'test_helper'

class BankAccountTest < ActiveSupport::TestCase
  def setup
    @bankAccount = BankAccount.new(client_id: 123412, balance: 1234, account_number: 'fsda7faskf' )
  end

  test 'bank account with empty client id' do
    @bankAccount.client_id = nil
    assert @bankAccount.invalid?
  end

  test 'bank account with empty account number' do
    @bankAccount.account_number = nil
    assert @bankAccount.invalid?
  end
  test 'bank account with uniqueness account number' do
    skip
    @bankAccount.account_number = 'fsda7faskf'
    assert @bankAccount.invalid?
  end
end

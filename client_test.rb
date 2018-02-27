require 'test_helper'

class UserTest < ActiveSupport::TestCase
  def setup
    @client = Client.new(first_name: 'John', last_name: 'Doe', middle_name: 'Brown', client_number: 'lkfsd98lksdf78kl', email: 'john@example.com')
  end

  test 'invalid client first name' do
    @client.first_name = nil
    assert @client.invalid?
  end
  test 'invalid client last name' do
    @client.last_name = nil
    assert @client.invalid?
  end
  test 'invalid client middle name' do
    @client.middle_name = nil
    assert @client.invalid?
  end
  test 'invalid client email name' do
    @client.last_name = nil
    assert @client.invalid?
  end
  test 'valid client data' do
    assert @client.valid?
  end
end

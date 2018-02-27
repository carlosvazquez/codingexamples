<?php

namespace Shoperti\Tests\Shipping\Api;

use Shoperti\Shipping\Models\Account;
use Shoperti\Shipping\Models\Address;
use Shoperti\Shipping\Models\Customer;
use Shoperti\Tests\Shipping\Support\BeApiAccountTrait;

class CustomersTest extends AbstractApiTestCase
{
    use BeApiAccountTrait;

    /** @test */
    public function it_should_return_apikey_missing_in_show_customer()
    {
        $this->get('v1/customers');

        $this->seeJsonStructure(['error' => [
            'message',
            'type',
        ]]);

        $this->seeJson(['message' => 'You did not provide an API key.']);
    }

    /** @test */
    public function it_should_return_only_customers_in_livemode()
    {
        $additional_customers = factory(Customer::class, 3)->create();

        $customers = factory(Customer::class, 3)->create([
            'account_id' => $this->account->id,
            'live'       => true,
        ]);

        $this->get('v1/customers', $this->setHeadersWithKey('live'));

        $this->seeJson(['id' => $customers[0]->id]);
        $this->seeJson(['id' => $customers[1]->id]);
        $this->seeJson(['id' => $customers[2]->id]);

        $this->dontSeeJson(['id' => $additional_customers[0]->id]);
        $this->dontSeeJson(['id' => $additional_customers[1]->id]);
        $this->dontSeeJson(['id' => $additional_customers[2]->id]);

        $this->seeJson(['total' => count($customers)]);

        $this->seeJson(['live' => true]);
        $this->dontSeeJson(['live' => false]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_return_only_customers_in_sandbox()
    {
        $additionalCustomers = factory(Customer::class, 3)->create();

        $customers = factory(Customer::class, 3)->create([
                'account_id' => $this->account->id,
                'live'       => false,
            ]);

        $this->get('v1/customers', $this->setHeadersWithKey('sandbox'));

        $this->seeJson(['id' => $customers[0]->id]);
        $this->seeJson(['id' => $customers[1]->id]);
        $this->seeJson(['id' => $customers[2]->id]);

        $this->dontSeeJson(['id' => $additionalCustomers[0]->id]);
        $this->dontSeeJson(['id' => $additionalCustomers[1]->id]);
        $this->dontSeeJson(['id' => $additionalCustomers[2]->id]);

        $this->seeJson(['total' => count($customers)]);

        $this->seeJson(['live' => false]);
        $this->dontSeeJson(['live' => true]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_return_a_customer_entity_in_livemode()
    {
        $customer = factory(Customer::class)->create([
                'account_id' => $this->account->id,
                'live'       => true,
            ]);

        $address = factory(Address::class)->create([
            'account_id'       => $this->account->id,
            'live'             => true,
            'addressable_id'   => $customer->id,
            'addressable_type' => get_class($customer),
        ]);

        $this->get('v1/customers/'.$customer->id, $this->setHeadersWithKey('live'));

        $this->seeCustomerJson($customer);

        $this->seeJson([
            'object' => 'customer',
            'id'     => $address->id,
            'object' => $address->object,
            'live'   => $customer->live,
        ]);
    }

    /** @test */
    public function it_should_return_a_customer_entity_in_sandbox()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => false,
        ]);

        $address = factory(Address::class)->create([
            'account_id'       => $this->account->id,
            'live'             => $customer->live,
            'addressable_id'   => $customer->id,
            'addressable_type' => get_class($customer),
        ]);

        $this->get('v1/customers/'.$customer->id, $this->setHeadersWithKey('sandbox'));

        $this->seeCustomerJson($customer);

        $this->seeJson([
            'object' => 'customer',
            'id'     => $address->id,
            'object' => $address->object,
            'live'   => $address->live,
        ]);
    }

    /** @test */
    public function it_should_add_a_customer_in_sandbox()
    {
        $customer = factory(Customer::class)->make();

        $data = $this->getNewCustomerData($customer);

        $this->post('v1/customers', $data, $this->setHeadersWithKey('sandbox'));

        $this->seeCustomerJson($customer);

        $this->seeJson([
            'live' => false,
        ]);
    }

    /** @test */
    public function it_should_add_a_customer_in_livemode()
    {
        $customer = factory(Customer::class)->make([
            'account_id' => $this->account->id,
            'live'       => true,
        ]);

        $data = $this->getNewCustomerData($customer);

        $this->post('v1/customers', $data, $this->setHeadersWithKey('live', false));
        $this->seeCustomerJson($customer);

        $this->seeJson([
            'live'            => true,
            'name'            => $customer->name,
            'email'           => $customer->email,
            'is_delinquent'   => false,
            'default_address' => null,
        ]);
    }

    /** @test */
    public function it_should_change_customer_information_in_sandbox()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => false,
        ]);

        $newdata['name'] = 'John Doe';
        $newdata['email'] = 'john.doe@example.com';

        $this->put('v1/customers/'.$customer->id, $newdata, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'live'  => $customer->live,
            'name'  => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);
        $this->assertResponseStatus(200);
    }

    /** @test */
    public function it_should_change_customer_information_in_livemode()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => true,
        ]);

        $newdata['name'] = 'John Doe';
        $newdata['email'] = 'john.doe@example.com';

        $this->put('v1/customers/'.$customer->id, $newdata, $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'live'  => $customer->live,
            'name'  => 'John Doe',
            'email' => 'john.doe@example.com',
        ]);
        $this->assertResponseStatus(200);
    }

    /** @test */
    public function it_should_not_change_customer_information_with_invalid_key_in_sandbox()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => true,
        ]);

        $this->put('v1/customers/'.$customer->id, [], $this->setHeadersWithKey('sandbox'));

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function it_should_not_change_customer_information_with_invalid_key_in_livemode()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => false,
        ]);

        $this->put('v1/customers/'.$customer->id, [], $this->setHeadersWithKey('live'));

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function it_should_remove_a_customer_in_sandbox()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => false,
        ]);

        $this->delete('v1/customers/'.$customer->id, [], $this->setHeadersWithKey('sandbox'));

        $this->assertResponseStatus(200);
        $this->seeJson([
            'message' => 'Customer was successfully deleted',
        ]);

        $deletedCustomer = Customer::withTrashed()->find($customer->id);

        $this->assertNotNull($deletedCustomer['deleted_at']);
    }

    /** @test */
    public function it_should_remove_a_customer_in_livemode()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => true,
        ]);

        $this->delete('v1/customers/'.$customer->id, [], $this->setHeadersWithKey('live'));

        $this->assertResponseStatus(200);
        $this->seeJson([
            'message' => 'Customer was successfully deleted',
        ]);

        $deletedCustomer = Customer::withTrashed()->find($customer->id);

        $this->assertNotNull($deletedCustomer['deleted_at']);
    }

    /** @test */
    public function it_should_not_remove_customer_because_of_different_livemode()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => true,
        ]);

        $this->delete('v1/customers/'.$customer->id, [], $this->setHeadersWithKey('sandbox'));

        $this->assertResponseStatus(404);
        $this->seeJson([
            'status' => 404,
            'title'  => 'Not Found',
        ]);

        $deletedCustomer = Customer::withTrashed()->find($customer->id);

        $this->assertNull($deletedCustomer['deleted_at']);
    }

    /** @test */
    public function it_should_assign_a_default_address_to_customer_in_sandbox()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => false,
        ]);

        $address = factory(Address::class)->create([
            'account_id'       => $this->account->id,
            'live'             => false,
            'addressable_id'   => $customer->id,
            'addressable_type' => get_class($customer),
        ]);

        $newdata['default_address'] = $address->id;

        $this->put('v1/customers/'.$customer->id, $newdata, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'live'            => $customer->live,
            'default_address' => $address->id,
        ]);
        $this->assertResponseStatus(200);
    }

    /** @test */
    public function it_should_assign_a_default_address_to_customer_in_livemode()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => true,
        ]);

        $address = factory(Address::class)->create([
            'account_id'       => $this->account->id,
            'live'             => true,
            'addressable_id'   => $customer->id,
            'addressable_type' => get_class($customer),
        ]);

        $newdata['default_address'] = $address->id;

        $this->put('v1/customers/'.$customer->id, $newdata, $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'live'            => $customer->live,
            'default_address' => $address->id,
        ]);
        $this->assertResponseStatus(200);
    }

    /** @test */
    public function it_should_not_assign_a_default_address_to_customer_with_incorrect_key_in_sandbox()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => false,
        ]);

        $address = factory(Address::class)->create([
            'account_id'       => $this->account->id,
            'live'             => false,
            'addressable_id'   => $customer->id,
            'addressable_type' => get_class($customer),
        ]);

        $newdata['default_address'] = $address->id;

        $this->put('v1/customers/'.$customer->id, $newdata, $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'title' => 'Not Found',
        ]);
        $this->assertResponseStatus(404);
    }

    /** @test */
    public function it_should_not_assign_a_default_address_customer_with_incorrect_key_in_livemode()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => true,
        ]);

        $address = factory(Address::class)->create([
            'account_id'       => $this->account->id,
            'live'             => true,
            'addressable_id'   => null,
            'addressable_type' => null,
        ]);

        $newdata['default_address'] = $address->id;

        $this->put('v1/customers/'.$customer->id, $newdata, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'title' => 'Not Found',
        ]);
        $this->assertResponseStatus(404);
    }

    /** @test */
    public function it_should_not_assign_a_default_address_to_customer_because_belongs_to_different_account_in_sandbox()
    {
        $otherAccount = factory(Account::class)->create();

        $account = factory(Account::class)->create();

        $customer = factory(Customer::class)->create([
            'account_id' => $account->id,
            'live'       => false,
        ]);

        $address = factory(Address::class)->create([
            'account_id'       => $otherAccount->id,
            'live'             => false,
            'addressable_id'   => null,
            'addressable_type' => null,
        ]);

        $newdata['default_address'] = $address->id;

        $this->put('v1/customers/'.$customer->id, $newdata, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'title' => 'Not Found',
        ]);
        $this->assertResponseStatus(404);
    }

    /** @test */
    public function it_should_flag_customer_as_a_delinquent_in_sandbox()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => false,
        ]);

        $data['flag'] = true;

        $this->post('v1/customers/'.$customer->id.'/delinquent', $data, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'live'          => false,
            'is_delinquent' => true,
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function it_should_unflag_customer_as_a_delinquent_in_sandbox()
    {
        $customer = factory(Customer::class)->create([
            'account_id'    => $this->account->id,
            'live'          => false,
            'is_delinquent' => true,
        ]);

        $data['flag'] = false;

        $this->post('v1/customers/'.$customer->id.'/delinquent', $data, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'live'          => false,
            'is_delinquent' => false,
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function it_should_flag_customer_as_a_delinquent_in_livemode()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => true,
        ]);

        $data['flag'] = true;

        $this->post('v1/customers/'.$customer->id.'/delinquent', $data, $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'live'          => true,
            'is_delinquent' => true,
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function it_should_unflag_customer_as_a_delinquent_in_livemode()
    {
        $customer = factory(Customer::class)->create([
            'account_id'    => $this->account->id,
            'live'          => true,
            'is_delinquent' => true,
        ]);

        $data['flag'] = false;

        $this->post('v1/customers/'.$customer->id.'/delinquent', $data, $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'live'          => true,
            'is_delinquent' => false,
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function it_should_flag_customer_as_a_delinquent_without_param_in_sandbox()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => false,
        ]);

        $this->post('v1/customers/'.$customer->id.'/delinquent', [], $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'live'          => false,
            'is_delinquent' => true,
        ]);

        $this->assertResponseStatus(200);
    }

    /** @test */
    public function it_should_flag_customer_as_a_delinquent_without_param_in_livemode()
    {
        $customer = factory(Customer::class)->create([
            'account_id' => $this->account->id,
            'live'       => true,
        ]);

        $this->post('v1/customers/'.$customer->id.'/delinquent', [], $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'live'          => true,
            'is_delinquent' => true,
        ]);

        $this->assertResponseStatus(200);
    }

    private function getNewCustomerData($customer)
    {
        $data = [
            'name'            => $customer->name,
            'email'           => $customer->email,
            'is_delinquent'   => $customer->is_delinquent,
            'default_address' => $customer->default_address,
        ];

        return $data;
    }

    private function seeCustomerJson($customer)
    {
        $this->seeJsonStructure(['data' => [
            'id',
        ]]);

        $this->seeJson([
            'name'            => $customer->name,
            'email'           => $customer->email,
            'is_delinquent'   => (bool) $customer->is_delinquent,
            'default_address' => $customer->default_address,
        ]);
    }
}

<?php

namespace Shoperti\Tests\Shipping\Api;

use Shoperti\Shipping\Models\Account;
use Shoperti\Shipping\Models\ParcelTemplate;
use Shoperti\Tests\Shipping\Support\BeApiAccountTrait;

class ParcelTemplatesTest  extends AbstractApiTestCase
{
    use BeApiAccountTrait;

    /** @test */
    public function it_should_throw_an_error_on_apikey_missing()
    {
        $this->get('v1/parceltemplates');

        $this->seeJsonStructure(['error' => [
            'message',
            'type',
        ]]);

        $this->seeJson(['message' => 'You did not provide an API key.']);
    }

    /** @test */
    public function it_should_display_all_parcel_templates_related_to_account_or_null_in_livemode()
    {
        $parcels = factory(ParcelTemplate::class, 3)->create([
            'account_id' => $this->account->id
        ]);

        $parcelsWithoutOwner = factory(ParcelTemplate::class)->create([
            'account_id' => null
        ]);

        $account = factory(Account::class)->create();

        $parcelsWithOtherOwner = factory(ParcelTemplate::class)->create([
            'account_id' => $account->id
        ]);

        $this->get('v1/parceltemplates', $this->setHeadersWithKey('live', false));

        $this->seeJson(['id' => $parcels[0]->id]);
        $this->seeJson(['id' => $parcels[1]->id]);
        $this->seeJson(['id' => $parcels[2]->id]);
        $this->seeJson(['id' => $parcelsWithoutOwner->id]);
        $this->seeJson(['total' => count($parcels) + count($parcelsWithoutOwner)]);
        $this->dontSeeJson(['id' => $parcelsWithOtherOwner->id]);
        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_display_a_specific_parcel_template_in_sandbox()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $this->account->id,
        ]);

        $this->get('v1/parceltemplates/'.$parcelTemplate->id, $this->setHeadersWithKey('sandbox', false));

        $this->seeParcelTemplateJson($parcelTemplate);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_display_a_specific_parcel_template_in_live()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $this->account->id,
        ]);

        $this->get('v1/parceltemplates/'.$parcelTemplate->id, $this->setHeadersWithKey('live', false));

        $this->seeParcelTemplateJson($parcelTemplate);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_display_a_specific_parcel_template_using_slug_in_sandbox()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id'=> $this->account->id,
            'name'      => 'Hello Planet',
            'slug'      => 'hello_planet'
        ]);

        $this->get('v1/parceltemplates/'.$parcelTemplate->slug, $this->setHeadersWithKey('sandbox', false));

        $this->assertResponseOk();
        $this->seeJson([
            'id'            => $parcelTemplate->id,
            'name'          => $parcelTemplate->name,
            'slug'          => $parcelTemplate->slug,
            'length'        => number_format((float) $parcelTemplate->length, 2, '.', ''),
            'width'         => number_format((float) $parcelTemplate->width, 2, '.', ''),
            'height'        => number_format((float) $parcelTemplate->height, 2, '.', ''),
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => number_format((float) $parcelTemplate->weight, 2, '.', ''),
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);
    }

    /** @test */
    public function it_should_display_a_specific_parcel_template_using_slug_in_live()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id'=> $this->account->id,
            'name'      => 'Hello Planet',
            'slug'      => 'hello_planet'
        ]);

        $this->get('v1/parceltemplates/'.$parcelTemplate->slug, $this->setHeadersWithKey('live', false));

        $this->assertResponseOk();
        $this->seeJson([
            'id'            => $parcelTemplate->id,
            'name'          => $parcelTemplate->name,
            'slug'          => $parcelTemplate->slug,
            'length'        => number_format((float) $parcelTemplate->length, 2, '.', ''),
            'width'         => number_format((float) $parcelTemplate->width, 2, '.', ''),
            'height'        => number_format((float) $parcelTemplate->height, 2, '.', ''),
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => number_format((float) $parcelTemplate->weight, 2, '.', ''),
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);
    }

    /** @test */
    public function it_should_display_a_specific_parcel_template_using_id_in_sandbox()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id'=> $this->account->id,
            'name'      => 'Hello Planet',
            'slug'      => 'hello_planet'
        ]);

        $this->get('v1/parceltemplates/'.$parcelTemplate->id, $this->setHeadersWithKey('sandbox', false));

        $this->assertResponseOk();
        $this->seeJson([
            'id'            => $parcelTemplate->id,
            'name'          => 'Hello Planet',
            'slug'          => 'hello_planet',
            'length'        => number_format((float) $parcelTemplate->length, 2, '.', ''),
            'width'         => number_format((float) $parcelTemplate->width, 2, '.', ''),
            'height'        => number_format((float) $parcelTemplate->height, 2, '.', ''),
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => number_format((float) $parcelTemplate->weight, 2, '.', ''),
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);
    }

    /** @test */
    public function it_should_display_a_specific_parcel_template_using_id_in_live()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id'=> $this->account->id,
            'name'      => 'Hello Planet',
            'slug'      => 'hello_planet'
        ]);

        $this->get('v1/parceltemplates/'.$parcelTemplate->id, $this->setHeadersWithKey('live', false));

        $this->assertResponseOk();
        $this->seeJson([
            'id'            => $parcelTemplate->id,
            'name'          => 'Hello Planet',
            'slug'          => 'hello_planet',
            'length'        => number_format((float) $parcelTemplate->length, 2, '.', ''),
            'width'         => number_format((float) $parcelTemplate->width, 2, '.', ''),
            'height'        => number_format((float) $parcelTemplate->height, 2, '.', ''),
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => number_format((float) $parcelTemplate->weight, 2, '.', ''),
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);
    }

    /** @test */
    public function it_should_not_display_a_specific_parcel_template_using_id_from_other_account_in_sandbox()
    {
        $account = factory(Account::class)->create();

        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id'=> $account->id,
            'name'      => 'Hello Planet',
            'slug'      => 'hello_planet'
        ]);

        $this->get('v1/parceltemplates/'.$parcelTemplate->id, $this->setHeadersWithKey('sandbox', false));

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function it_should_not_display_a_specific_parcel_template_using_id_from_other_account_in_live()
    {
        $account = factory(Account::class)->create();

        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id'=> $account->id,
            'name'      => 'Hello Planet',
            'slug'      => 'hello_planet'
        ]);

        $this->get('v1/parceltemplates/'.$parcelTemplate->id, $this->setHeadersWithKey('live', false));

        $this->assertResponseStatus(404);
    }

    /** @test */
    public function it_should_create_a_new_parcel_template_sandbox()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->make([
        ]);

        $data = $this->getParcelTemplateDataArray($parcelTemplate);

        $this->post('v1/parceltemplates', $data, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'name'          => $parcelTemplate->name,
            'slug'          => $parcelTemplate->slug,
            'length'        => $parcelTemplate->length,
            'width'         => $parcelTemplate->width,
            'height'        => $parcelTemplate->height,
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => $parcelTemplate->weight,
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_create_a_new_parcel_template_live()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->make([
            'name' => 'Hello World',
            'slug' => 'hello_world'
        ]);

        $data = $this->getParcelTemplateDataArray($parcelTemplate);

        $this->post('v1/parceltemplates', $data, $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'name'          => $parcelTemplate->name,
            'slug'          => $parcelTemplate->slug,
            'length'        => $parcelTemplate->length,
            'width'         => $parcelTemplate->width,
            'height'        => $parcelTemplate->height,
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => $parcelTemplate->weight,
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_create_a_new_parcel_template_with_existing_slug_sandbox()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $this->account->id,
            'name' => 'Hello World',
            'slug' => 'hello_world'
        ]);

        $data = $this->getParcelTemplateDataArray($parcelTemplate);

        $this->post('v1/parceltemplates', $data, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'name'          => $parcelTemplate->name,
            'slug'          => 'hello_world_1',
            'length'        => $parcelTemplate->length,
            'width'         => $parcelTemplate->width,
            'height'        => $parcelTemplate->height,
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => $parcelTemplate->weight,
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_create_a_new_parcel_template_with_existing_slug_live()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $this->account->id,
            'name' => 'Hello World',
            'slug' => 'hello_world'
        ]);

        $data = $this->getParcelTemplateDataArray($parcelTemplate);

        $this->post('v1/parceltemplates', $data, $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'name'          => $parcelTemplate->name,
            'slug'          => 'hello_world_1',
            'length'        => $parcelTemplate->length,
            'width'         => $parcelTemplate->width,
            'height'        => $parcelTemplate->height,
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => $parcelTemplate->weight,
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_change_own_parcel_template_in_sandbox()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $this->account->id,
        ]);

        $data = [
            'name'          => 'Hello DinkBit',
            'slug'          => 'hello_dinkbit',
            'length'        => 24,
            'width'         => 50,
            'height'        => 14,
            'distance_unit' => 'cm',
            'weight'        => 1,
            'weight_unit'   => 'kg',
        ];

        $this->put('v1/parceltemplates/'.$parcelTemplate->id, $data, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'name'          => 'Hello DinkBit',
            'slug'          => 'hello_dinkbit',
            'length'        => 24,
            'width'         => 50,
            'height'        => 14,
            'distance_unit' => 'cm',
            'weight'        => 1,
            'weight_unit'   => 'kg',
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_create_a_new_parcel_template_with_empty_slug_in_live()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'name' => 'Hello planet',
            'slug' => ''
        ]);

        $data = $this->getParcelTemplateDataArray($parcelTemplate);

        $this->post('v1/parceltemplates', $data, $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'name'          => $parcelTemplate->name,
            'slug'          => 'hello_planet',
            'length'        => $parcelTemplate->length,
            'width'         => $parcelTemplate->width,
            'height'        => $parcelTemplate->height,
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => $parcelTemplate->weight,
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_change_own_parcel_template_in_live()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $this->account->id,
        ]);

        $data = [
            'name'          => 'Hello DinkBit',
            'slug'          => 'hello_dinkbit',
            'length'        => 24,
            'width'         => 50,
            'height'        => 14,
            'distance_unit' => 'cm',
            'weight'        => 1,
            'weight_unit'   => 'kg',
        ];

        $this->put('v1/parceltemplates/'.$parcelTemplate->id, $data, $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'name'          => 'Hello DinkBit',
            'slug'          => 'hello_dinkbit',
            'length'        => 24,
            'width'         => 50,
            'height'        => 14,
            'distance_unit' => 'cm',
            'weight'        => 1,
            'weight_unit'   => 'kg',
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_create_a_new_parcel_template_with_empty_slug_in_sandbox()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'name' => 'Hello World',
            'slug' => ''
        ]);

        $data = $this->getParcelTemplateDataArray($parcelTemplate);

        $this->post('v1/parceltemplates', $data, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'name'          => $parcelTemplate->name,
            'slug'          => 'hello_world',
            'length'        => $parcelTemplate->length,
            'width'         => $parcelTemplate->width,
            'height'        => $parcelTemplate->height,
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => $parcelTemplate->weight,
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_not_change_parcel_template_from_other_account_sandbox()
    {
        $account = factory(Account::class)->create([
            'id' => 'ac_cirhxq1st0000gyrrj0phobuz',
        ]);

        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $account->id,
        ]);

        $data = [
            'name' => 'Hello DinkBit',
            'slug' => 'hello_dinkbit',
        ];

        $this->put('v1/parceltemplates/'.$parcelTemplate->id, $data, $this->setHeadersWithKey('sandbox', false));

        $this->assertResponseStatus(404);
        $this->seeJson([
            'title' => "Not Found",
        ]);
    }

    /** @test */
    public function it_should_not_change_parcel_template_from_other_account_live()
    {
        $account = factory(Account::class)->create([
            'id' => 'ac_cirhxq1st0000gyrrj0phobuz',
        ]);

        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $account->id,
        ]);

        $data = [
            'name' => 'Hello DinkBit',
            'slug' => 'hello_dinkbit',
        ];

        $this->put('v1/parceltemplates/'.$parcelTemplate->id, $data, $this->setHeadersWithKey('live', false));

        $this->assertResponseStatus(404);
        $this->seeJson([
            'title' => "Not Found",
        ]);

    }

    /** @test */
    public function it_should_change_own_parcel_template_with_new_slug_in_sandbox()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $this->account->id,
            'name'          => 'Hello Planet',
            'slug'          => 'hello_planet',
        ]);

        $data = [
            'name'          => 'Hello DinkBit',
            'slug'          => 'hello_dinkbit',
            'length'        => 24,
            'width'         => 50,
            'height'        => 14,
            'distance_unit' => 'cm',
            'weight'        => 1,
            'weight_unit'   => 'kg',
        ];

        $this->put('v1/parceltemplates/'.$parcelTemplate->id, $data, $this->setHeadersWithKey('sandbox', false));

        $this->seeJson([
            'name'          => 'Hello DinkBit',
            'slug'          => 'hello_dinkbit',
            'length'        => 24,
            'width'         => 50,
            'height'        => 14,
            'distance_unit' => 'cm',
            'weight'        => 1,
            'weight_unit'   => 'kg',
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_change_own_parcel_template_with_new_slug_in_live()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $this->account->id,
            'name'          => 'Hello Planet',
            'slug'          => 'hello_planet',
        ]);

        $data = [
            'name'          => 'Hello DinkBit',
            'slug'          => 'hello_dinkbit',
            'length'        => 24,
            'width'         => 50,
            'height'        => 14,
            'distance_unit' => 'cm',
            'weight'        => 1,
            'weight_unit'   => 'kg',
        ];

        $this->put('v1/parceltemplates/'.$parcelTemplate->id, $data, $this->setHeadersWithKey('live', false));

        $this->seeJson([
            'name'          => 'Hello DinkBit',
            'slug'          => 'hello_dinkbit',
            'length'        => 24,
            'width'         => 50,
            'height'        => 14,
            'distance_unit' => 'cm',
            'weight'        => 1,
            'weight_unit'   => 'kg',
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_should_remove_parcel_template_from_own_account_sandbox()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $this->account->id
        ]);

        $this->delete('v1/parceltemplates/'.$parcelTemplate->id, [], $this->setHeadersWithKey('sandbox', false));
        $this->assertResponseStatus(200);

        $this->seeJson([
            'message'          => 'Parcel Template was successfully deleted'
        ]);
    }

    /** @test */
    public function it_should_remove_parcel_template_from_own_account_livemode()
    {
        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $this->account->id
        ]);

        $this->delete('v1/parceltemplates/'.$parcelTemplate->id, [], $this->setHeadersWithKey('live', false));
        $this->assertResponseStatus(200);

        $this->seeJson([
            'message'          => 'Parcel Template was successfully deleted'
        ]);
    }

    /** @test */
    public function it_should_not_remove_parcel_template_from_own_account_sandbox()
    {
        $account = factory(Account::class)->create();

        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $account->id
        ]);

        $this->delete('v1/parceltemplates/'.$parcelTemplate->id, [], $this->setHeadersWithKey('sandbox', false));
        $this->assertResponseStatus(404);

        $this->seeJson([
            'title'          => 'Not Found'
        ]);
    }

    /** @test */
    public function it_should_not_remove_parcel_template_from_own_account_livemode()
    {
        $account = factory(Account::class)->create();

        $parcelTemplate = factory(ParcelTemplate::class)->create([
            'account_id' => $account->id
        ]);

        $this->delete('v1/parceltemplates/'.$parcelTemplate->id, [], $this->setHeadersWithKey('live', false));
        $this->assertResponseStatus(404);

        $this->seeJson([
            'title'          => 'Not Found'
        ]);
    }

    /**
     * @param array $parcelTemplate
     *
     * @return array
     */
    private function seeParcelTemplateJson($parcelTemplate)
    {
        $this->seeJsonStructure(['data' => [
            'id',
        ]]);

        $this->seeJson([
            'id'            => $parcelTemplate->id,
            'name'          => $parcelTemplate->name,
            'slug'          => $parcelTemplate->slug,
            'length'        => number_format((float) $parcelTemplate->length, 2, '.', ''),
            'width'         => number_format((float) $parcelTemplate->width, 2, '.', ''),
            'height'        => number_format((float) $parcelTemplate->height, 2, '.', ''),
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => number_format((float) $parcelTemplate->weight, 2, '.', ''),
            'weight_unit'   => $parcelTemplate->weight_unit,
        ]);
    }

    private function getParcelTemplateDataArray($parcelTemplate)
    {
        $data = [
            'account_id'    => $parcelTemplate->account_id,
            'name'          => $parcelTemplate->name,
            'slug'          => $parcelTemplate->slug,
            'length'        => $parcelTemplate->length,
            'width'         => $parcelTemplate->width,
            'height'        => $parcelTemplate->height,
            'distance_unit' => $parcelTemplate->distance_unit,
            'weight'        => $parcelTemplate->weight,
            'weight_unit'   => $parcelTemplate->weight_unit,
        ];

        return $data;
    }
}

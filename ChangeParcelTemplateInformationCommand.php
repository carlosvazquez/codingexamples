<?php

namespace Shoperti\Shipping\Bus\Commands\ParcelTemplate;

/**
 * This is the change parcel template command class.
 *
 * @author Carlos Vazquez <carlos@shoperti.com>
 */
class ChangeParcelTemplateInformationCommand
{
    /**
     * The Parcel Template instance.
     *
     * @var \Shoperti\Shipping\Models\ParcelTemplate
     */
    public $parcelTemplate;

    /**
     * The parcel template name.
     *
     * @var string
     */
    public $name;

    /**
     * The parcel template slug.
     *
     * @var string
     */
    public $slug;

    /**
     * The length parcel template.
     *
     * @var int
     */
    public $length;

    /**
     * The width parcel template.
     *
     * @var int
     */
    public $width;

    /**
     * The height parcel template.
     *
     * @var int
     */
    public $height;

    /**
     * The distance unit for parcel template.
     *
     * @var string
     */
    public $distance_unit;

    /**
     * The weight for parcel template.
     *
     * @var int
     */
    public $weight;

    /**
     * The weight unit for parcel template.
     *
     * @var string
     */
    public $weight_unit;

    /**
     * The command rules.
     *
     * @var array
     */
    public $rules = [
        'name'          => 'required|string|max:150',
        'slug'          => 'string|unique:parcel_templates|max:150',
        'length'        => 'required|numeric|min:0.001',
        'width'         => 'required|numeric|min:0.001',
        'height'        => 'required|numeric|min:0.001',
        'distance_unit' => 'required|string|max:2|in:mm,cm,m,in,ft,yd',
        'weight'        => 'required|numeric|min:0.001',
        'weight_unit'   => 'required|string|max:2|in:g,kg,oz,lb'
    ];

    /**
     * Creates a new parcel template collection image command.
     *
     * @param $parcelTemplate
     * @param $name
     * @param $slug
     * @param $length
     * @param $width
     * @param $height
     * @param $distance_unit
     * @param $weight
     * @param $weight_unit
     *
     */
    public function __construct(
        $parcelTemplate,
        $name,
        $slug,
        $length,
        $width,
        $height,
        $distance_unit,
        $weight,
        $weight_unit
    ) {
        $this->parcelTemplate = $parcelTemplate;
        $this->name = $name;
        $this->slug = $slug;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->distance_unit = $distance_unit;
        $this->weight = $weight;
        $this->weight_unit = $weight_unit;
    }
}

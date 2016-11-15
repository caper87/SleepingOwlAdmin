<?php

namespace SleepingOwl\Admin\Form\Element;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DateRange extends Date
{
    /**
     * @var string
     */
    protected $format = 'Y-m-d';

    /**
     * @var string
     */
    protected $defaultFrom;

    /**
     * @var string
     */
    protected $defaultTo;

    /**
     * @return string
     */
    public function getDefaultFrom()
    {
        if (! $this->defaultFrom) {
            $this->defaultFrom = Carbon::now();
        }

        return $this->defaultFrom instanceof \DateTime
            ? $this->defaultFrom->format(config('sleeping_owl.dateFormat', $this->getPickerFormat()))
            : $this->defaultFrom;
    }

    /**
     * @param string $defaultFrom
     *
     * @return $this
     */
    public function setDefaultFrom($defaultFrom)
    {
        $this->defaultFrom = $defaultFrom;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultTo()
    {
        if (! $this->defaultTo) {
            $this->defaultTo = Carbon::now();
        }

        return $this->defaultTo instanceof \DateTime
            ? $this->defaultTo->format(config('sleeping_owl.dateFormat', $this->getPickerFormat()))
            : $this->defaultTo;
    }

    /**
     * @param string $defaultTo
     *
     * @return $this
     */
    public function setDefaultTo($defaultTo)
    {
        $this->defaultTo = $defaultTo;

        return $this;
    }

    /**
     * @param Model $model
     * @param string $attribute
     * @param mixed $value
     */
    public function setValue(Model $model, $attribute, $value)
    {
        $value = ! empty($value) ? array_map(function ($date) {
            return Carbon::createFromFormat($this->getPickerFormat(), $date);
        }, explode('::', $value)) : null;

        $model->setAttribute($attribute, $value);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return parent::toArray() + [
            'startDate' => $this->getDefaultFrom(),
            'endDate' => $this->getDefaultTo(),
        ];
    }
}

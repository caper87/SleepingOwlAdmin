<?php

namespace SleepingOwl\Admin\Factories;

use SleepingOwl\Admin\AliasBinder;
use SleepingOwl\Admin\Contracts\Display\DisplayColumnFilterFactoryInterface;
use SleepingOwl\Admin\Display\Column\Filter\Date;
use SleepingOwl\Admin\Display\Column\Filter\DateRange;
use SleepingOwl\Admin\Display\Column\Filter\Range;
use SleepingOwl\Admin\Display\Column\Filter\Select;
use SleepingOwl\Admin\Display\Column\Filter\Text;

/**
 * @method Text text()
 * @method Date date()
 * @method DateRange daterange()
 * @method Select select()
 * @method Range range()
 */
class DisplayColumnFilterFactory extends AliasBinder implements DisplayColumnFilterFactoryInterface
{

    /**
     * DisplayColumnFilterFactory constructor.
     *
     * @param \Illuminate\Contracts\Foundation\Application $application
     */
    public function __construct(\Illuminate\Contracts\Foundation\Application $application)
    {
        parent::__construct($application);

        $this->register([
            'text' => Text::class,
            'date' => Date::class,
            'daterange' => DateRange::class,
            'range' => Range::class,
            'select' => Select::class,
        ]);
    }
}

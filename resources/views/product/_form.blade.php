{!! form()->text('name')->label('Name')->required() !!}
{!! form()->text('type')->label('Type')->required() !!}
{!! form()->number('price')->label('Price')->min(0)->required() !!}
{!! form()->number('weight')->label('Weight (grams)')->min(0)->required() !!}

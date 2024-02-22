<?php namespace App;

use Cocur\Slugify\Slugify;

trait SlugableTrait {

    /**
     * Get the models slug's source column
     */
    public function getSlugSource()
    {
        if(!property_exists($this, 'slug_source'))
            throw new \Exception('slug_source property must be defined');
        return $this->slug_source;
    }

    /**
     * Get the name of the slug column or default to slug
     *
     * @return string
     */
    public function getSlugColumn()
    {
        return property_exists($this, 'slug_column')?$this->slug_column:'slug';
    }

    /**
     * Use slug source to generate a slug and check against the tables slug column
     */
    public function generateSlug()
    {
        $slugify = new Slugify();
        $value = $this->getAttribute($this->getSlugSource());
        if(!$value)
            throw new \InvalidArgumentException('Can\'t generate slug from Empty \''.  $this->getSlugSource() . '\' attribute');

        $slug = $slugify->slugify($value);

        // if len of slug is greater than 255 - truncate to 253 before we check for dups
        if(strlen($slug) > 255)
            $slug = substr($slug, 0, 255);

        // check for dup
        $num_dups = static::where($this->getSlugColumn(), 'LIKE', substr($slug, 0, 253) . '%')->count();

        if($num_dups > 0)
            $slug = substr($slug, 0, 253) . '-' . ($num_dups + 1);

        $this->setAttribute($this->getSlugColumn(), $slug);
        return $slug;
    }
}
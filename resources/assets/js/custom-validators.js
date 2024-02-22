import { Validator } from 'vee-validate';
import moment from 'moment';

Validator.extend('same_or_after', {
    getMessage: (field, params, data) => "The "+ field + ' must be a date after today',
    validate: (value, args) => {
        return moment(value).isAfter(moment(args[0]).subtract(1, 'days'), 'day');
    }
});

Validator.extend('nullable', {
    getMessage: (field, params, data) => "The "+ field + ' is nullable',
    validate: (value, args) => {
        return !value.length ? true: true;
    }
});
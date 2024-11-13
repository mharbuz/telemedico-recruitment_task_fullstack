// src/components/CurrencyRates.js
import React, { useEffect, useState } from 'react';
import axios from 'axios';

const CurrencyRates = () => {
    const [askedRates, setAskedRates] = useState([]);
    const [currentRates, setCurrentRates] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [askedDate, setAskedDate] = useState(null);

    /**
     * to simulate different dates, you can change the currentDate to a different date
     * eg
     * const [currentDate, setCurrentDate] = useState(new Date('2024-11-11'));
     */
    const [currentDate, setCurrentDate] = useState(new Date());

    useEffect(() => {
        fetchCurrentRates();
    }, []);

    useEffect(() => {
        if (askedDate !== null) {
        fetchAskedRates();
        }
    }, [askedDate]);

    const handleDateChange = (event) => {
        setAskedDate(new Date(event.target.value));
    };

    const fetchRates = async (date, setRates) => {
        setLoading(true);
        try {
            let rates = [];
            let stop = 20;
            let searchDate = date;

            while (rates.length === 0 && stop > 0) {
                stop--;
                let response = await fetchRatesForDate(searchDate);
                rates = response.data.currencies;
                searchDate.setDate(searchDate.getDate() - 1);
            }

            setRates(rates || []);
        } catch (error) {
            setError(error);
        } finally {
            setLoading(false);
        }
    };

    const fetchCurrentRates = () => {
        fetchRates(currentDate, setCurrentRates);
        setAskedDate(currentDate);

    };

    const fetchAskedRates = () => {
        fetchRates(askedDate, setAskedRates);
    };


    const formatDate = (date) => {
        return date.getFullYear() + '-' + String((date.getMonth() + 1)).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
    }

    const fetchRatesForDate = async (date) => {
        return await axios.get(`http://telemedi-zadanie.localhost/api/exchange-rates?date=${formatDate(date)}`);
    }

    if (loading) {
        return <div>Loading...</div>;
    }

    if (error) {
        return <div>Error: {error.message}</div>;
    }

    return (
        <div>
            <h1>Currency Rates</h1>
            <div className="mb-3 col-3">
                <label htmlFor="date">Select date:</label>
                <input className='form-control form-control-lg' type="date" value={formatDate(askedDate ?? new Date())} max={formatDate(new Date())} onChange={handleDateChange} />
            </div>
            
            {askedRates.length === 0 ? (
                <div className="alert alert-primary" role="alert">
                    No rates available for the selected date.
                </div>
            ) : (
                <table className='table table-striped'>
                    <thead>
                        <tr>
                            <th scole="col" className="p-3">Symbol</th>
                            <th scole="col" className="p-3">Your date rate</th>
                            <th scole="col" className="p-3">Current rate</th>
                        </tr>
                    </thead>
                    <tbody>
                    {askedRates.map((rate) => {
                        const currentRate = currentRates.find((currentRate) => currentRate.symbol === rate.symbol);
                        console.log(currentRate);
                        const rateClass = currentRate && currentRate.mediumRate > rate.mediumRate ? 'text-success' : currentRate && currentRate.mediumRate < rate.mediumRate ? 'text-danger' : '';
                        const currentToAskedRate = currentRate ? (100 - (rate.mediumRate / currentRate.mediumRate) * 100 ).toPrecision(2)  : 'N/A';
                        return (
                            <tr key={`${rate.symbol}`}>
                                <th scole="row" className="p-2">
                                    {rate.symbol} / PLN
                                </th>
                                <td className="p-2">
                                    {rate.mediumRate}
                                </td>
                                <td className="p-2">
                                    <small className={rateClass}>
                                        {currentRate ? currentRate.mediumRate : 'N/A'}&nbsp;
                                        ({currentToAskedRate}%)
                                    </small>
                                    </td>
                            </tr>
                        );
                    })}
                    </tbody>
                </table>
            )}
        </div>
    );
};

export default CurrencyRates;
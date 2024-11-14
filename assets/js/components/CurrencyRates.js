// src/components/CurrencyRates.js
import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { useHistory, useLocation } from 'react-router';

const CurrencyRates = () => {
    const [askedRates, setAskedRates] = useState([]);
    const [currentRates, setCurrentRates] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [askedDate, setAskedDate] = useState(null);
    const [currentDate, setCurrentDate] = useState(new Date());

    const history = useHistory();

    useEffect(() => {
        fetchCurrentRates();
    }, []);

    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const dateParam = urlParams.get('date');
        if (dateParam) {
            setAskedDate(new Date(dateParam));
        }
    }, []);

    useEffect(() => {
        if (askedDate !== null) {
            fetchAskedRates();
        }
    }, [askedDate]);

    const handleDateChange = (event) => {
        const newDate = new Date(event.target.value);
        setAskedDate(newDate);
        history.push({
            pathname: '/currency-rates',
            search: '?date=' + event.target.value
        })
    };

    const fetchRates = async (date, setRates, searchForCurrent) => {
        setLoading(true);
        try {
            let rates = [];
            let stop = searchForCurrent ? 20 : 1;
            let searchDate = date;

            while (rates.length === 0 && stop > 0) {
                stop--;
                let response = await fetchRatesForDate(searchDate);
                rates = response.data.currencies;
                if (stop > 0) {
                    searchDate.setDate(searchDate.getDate() - 1);
                }
            }

            setRates(rates || []);
        } catch (error) {
            setError(error);
        } finally {
            setLoading(false);
        }
    };

    const fetchCurrentRates = () => {
        fetchRates(currentDate, setCurrentRates, true);
        setAskedDate(currentDate);

    };

    const fetchAskedRates = () => {
        fetchRates(askedDate, setAskedRates, false);
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
                <input className='form-control form-control-lg' type="date" value={formatDate(askedDate ?? new Date())} 
                    max={formatDate(new Date())} min={formatDate(new Date('2023-01-01'))}
                    onChange={handleDateChange} />
            </div>
            
            {askedRates.length === 0 ? (
                <div className="alert alert-primary" role="alert">
                    No rates available for the selected date.
                </div>
            ) : (
                <table className='table table-striped'>
                    <thead>
                        <tr>
                            <th rowSpan="2" scole="col" className="p-3">Symbol</th>
                            <th colSpan="3" scole="col" className="p-3">Your date rate</th>
                            <th colSpan="3" scole="col" className="p-3">Current rate</th>
                        </tr>
                        <tr>
                            <th>Sell</th>
                            <th>Buy</th>
                            <th>Medium</th>
                            <th>Sell</th>
                            <th>Buy</th>
                            <th>Medium</th>
                        </tr>
                    </thead>
                    <tbody>
                    {askedRates.map((rate) => {
                        const currentRate = currentRates.find((currentRate) => currentRate.symbol === rate.symbol);
                        const rateClass = currentRate && currentRate.mediumRate > rate.mediumRate ? 'text-success' : currentRate && currentRate.mediumRate < rate.mediumRate ? 'text-danger' : '';
                        const currentToAskedRate = currentRate ? (100 - (rate.mediumRate / currentRate.mediumRate) * 100 ).toPrecision(2)  : 'N/A';
                        return (
                            <tr key={`${rate.symbol}`}>
                                <th scole="row" className="p-2">
                                    {rate.symbol} / PLN
                                </th>
                                <td className="p-2">
                                    {rate.sellPrice ? rate.sellPrice : '---' } 
                                </td>
                                <td>
                                    {rate.buyPrice ? (rate.buyPrice) : (
                                        <span title={`We don't buy ${rate.symbol}`}>---</span>
                                    )}
                                </td>
                                <td>
                                    {rate.mediumRate} 
                                </td>
                                <td className="p-2">
                                    {currentRate?.sellPrice ? currentRate.sellPrice : '---'}
                                </td>
                                <td>
                                    {currentRate?.buyPrice ? (currentRate.buyPrice) : (
                                        <span title={`We don't buy ${rate.symbol}`}>---</span>
                                    )}
                                </td>
                                <td className="p-2">
                                    <small className={rateClass}>
                                        {currentRate ? currentRate.mediumRate : '--'}&nbsp;
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
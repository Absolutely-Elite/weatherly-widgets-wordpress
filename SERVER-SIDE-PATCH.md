# FastAPI Server-Side Patch

Apply these changes on the server at `/opt/weatherlywidgets` to fix wind and state formatting in the API response. The WordPress plugin has PHP fallbacks, but fixing the API ensures consistent data for all consumers.

## 1. Add helper functions

Add these functions to your API module (e.g. `app/api/widget_routes.py` or a shared `app/utils/formatting.py`):

```python
def degrees_to_compass(degrees_str):
    """Convert wind direction degrees to compass direction."""
    try:
        degrees = float(degrees_str)
        directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE',
                      'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW']
        index = round(degrees / 22.5) % 16
        return directions[index]
    except (ValueError, TypeError):
        return degrees_str  # Return as-is if not a number

def format_wind_speed(speed_str):
    """Round wind speed to integer."""
    try:
        return str(round(float(speed_str)))
    except (ValueError, TypeError):
        return speed_str
```

## 2. Format response before returning JSON

In the endpoint(s) that serve `/api/v1/widget` and `/api/v1/wp-plugin`, before returning the response, apply:

```python
# Wind formatting
if 'current' in response_data and response_data['current']:
    curr = response_data['current']
    if 'wind_direction' in curr:
        curr['wind_direction'] = degrees_to_compass(curr['wind_direction'])
    if 'wind_speed' in curr:
        curr['wind_speed'] = format_wind_speed(curr['wind_speed'])

# State title case (if you have city_record or similar)
if 'state' in response_data and response_data['state']:
    response_data['state'] = response_data['state'].title()
# Or if from city_record:
# response_data['state'] = city_record.state_name.title() if city_record.state_name else city_record.state_name
```

## 3. Find the API files

```bash
grep -rl "wind_direction\|wind_speed\|/widget\|/wp-plugin" app/api/
```

## 4. After applying

```bash
systemctl restart weatherlywidgets
curl "https://weatherlywidgets.com/api/v1/widget?city=houston&state=TX" | python -m json.tool
```

Verify: `wind_direction` shows "SSW", `wind_speed` shows "9", `state` shows "Texas".

# https://github.com/elastic/kibana-docker
FROM docker.elastic.co/kibana/kibana:6.0.0

RUN rm /usr/share/kibana/config/kibana.yml
ADD kibana.yml /usr/share/kibana/config/

# Add your kibana plugins setup here
# Example: RUN kibana-plugin install <name|url>
